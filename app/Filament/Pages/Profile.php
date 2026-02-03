<?php

namespace App\Filament\Pages;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Hash;
use BackedEnum;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUser;

    protected static ?string $navigationLabel = 'Mi Perfil';

    protected static ?string $title = 'Mi Perfil';

    protected static ?int $navigationSort = 99;

    public ?array $data = [];
    
    protected string $view = 'filament.pages.profile';

    public function mount(): void
    {
        $user = auth()->user();
        
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'position' => $user->position,
            'birth_date' => $user->birth_date,
            'id_number' => $user->id_number,
            'avatar' => $user->avatar,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Información Personal')
                    ->description('Actualiza tu información personal')
                    ->schema([
                        FileUpload::make('avatar')
                            ->label('Foto de Perfil')
                            ->image()
                            ->avatar()
                            ->disk('public')
                            ->directory('avatars')
                            ->visibility('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                            ])
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg'])
                            ->maxSize(2048)
                            ->columnSpanFull(),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nombre Completo')
                                    ->required()
                                    ->maxLength(100),
                                
                                TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->email()
                                    ->required()
                                    ->maxLength(150),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('phone')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->maxLength(32),
                                
                                TextInput::make('id_number')
                                    ->label('Nº Identificación')
                                    ->maxLength(50),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                DatePicker::make('birth_date')
                                    ->label('Fecha de Nacimiento')
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->maxDate(now()->subYears(18)),
                                
                                TextInput::make('address')
                                    ->label('Dirección')
                                    ->maxLength(255),
                            ]),
                    ])
                    ->columns(1),

                Section::make('Información Laboral')
                    ->description('Datos de tu cargo')
                    ->schema([
                        TextInput::make('position')
                            ->label('Cargo/Posición')
                            ->maxLength(100)
                            ->disabled(fn () => auth()->user()->role !== 'admin')
                            ->helperText(auth()->user()->role === 'admin' ? 'Puedes editar tu cargo' : 'Contacta al administrador para cambiar tu cargo'),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Section::make('Cambiar Contraseña')
                    ->description('Actualiza tu contraseña de acceso')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Contraseña Actual')
                            ->password()
                            ->revealable()
                            ->required(fn ($get) => filled($get('password')))
                            ->dehydrated(false)
                            ->helperText('Requerida solo si deseas cambiar tu contraseña'),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('password')
                                    ->label('Nueva Contraseña')
                                    ->password()
                                    ->minLength(8)
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->revealable()
                                    ->helperText('Dejar en blanco para mantener la actual'),
                                
                                TextInput::make('password_confirmation')
                                    ->label('Confirmar Contraseña')
                                    ->password()
                                    ->same('password')
                                    ->dehydrated(false)
                                    ->revealable()
                                    ->required(fn ($get) => filled($get('password'))),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();

        // Verificar contraseña actual solo si se está intentando cambiar la contraseña
        if (isset($data['password']) && filled($data['password'])) {
            // Si se está cambiando la contraseña, verificar que se haya proporcionado la actual
            if (!isset($data['current_password']) || !filled($data['current_password'])) {
                Notification::make()
                    ->title('Error')
                    ->danger()
                    ->body('Debes ingresar tu contraseña actual para cambiarla.')
                    ->send();
                return;
            }
            
            // Verificar que la contraseña actual sea correcta
            if (!Hash::check($data['current_password'], $user->password)) {
                Notification::make()
                    ->title('Error')
                    ->danger()
                    ->body('La contraseña actual es incorrecta.')
                    ->send();
                return;
            }
        }

        // Actualizar datos
        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'position' => $data['position'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'id_number' => $data['id_number'] ?? null,
        ];

        // Solo actualizar avatar si se proporcionó uno nuevo
        if (isset($data['avatar']) && filled($data['avatar'])) {
            $updateData['avatar'] = $data['avatar'];
        }

        // Solo actualizar contraseña si se proporcionó una nueva
        if (isset($data['password']) && filled($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        // Recargar el usuario para obtener los datos actualizados
        $user->refresh();

        // Disparar evento para actualizar el avatar en toda la página
        $this->dispatch('profile-updated');

        Notification::make()
            ->title('Perfil actualizado')
            ->success()
            ->body('Tu información ha sido actualizada correctamente.')
            ->send();
            
        // Recargar el formulario con los datos actualizados
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'position' => $user->position,
            'birth_date' => $user->birth_date,
            'id_number' => $user->id_number,
            'avatar' => $user->avatar,
        ]);
    }
}
