<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información Personal')
                    ->description('Datos básicos del usuario')
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
                                Select::make('document_type')
                                    ->label('Tipo de Documento')
                                    ->native(true)
                                    ->options([
                                        'CC' => 'Cédula de Ciudadanía',
                                        'CE' => 'Cédula de Extranjería',
                                        'PA' => 'Pasaporte',
                                    ])
                                    ->required()
                                    ->maxLength(2), 
                                TextInput::make('document_number')
                                    ->label('Número de Documento')
                                    ->required()
                                    ->maxLength(10),

                                TextInput::make('name')
                                    ->label('Nombre Completo')
                                    ->required()
                                    ->maxLength(100),
                                
                                TextInput::make('email')
                                    ->label('Correo Electrónico')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(150),
                            ]),
                        
                        Grid::make(2)
                            ->schema([
                                TextInput::make('phone')
                                    ->label('Teléfono')
                                    ->tel()
                                    ->maxLength(10),
                                
                                TextInput::make('id_number')
                                    ->label('Nº Identificación')
                                    ->maxLength(10),
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
                    ->description('Datos del cargo y rol en el sistema')
                    ->schema([
                        TextInput::make('position')
                            ->label('Cargo/Posición')
                            ->maxLength(100),
                        
                        DatePicker::make('hire_date')
                            ->label('Fecha de Contratación')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now()),
                        
                        Select::make('role')
                            ->label('Rol del Sistema')
                            ->options([
                                'admin' => 'Administrador',
                                'tech' => 'Trabajador',
                            ])
                            ->default('tech')
                            ->required()
                            ->helperText('Administradores tienen acceso completo al sistema'),
                        
                        Select::make('status')
                            ->label('Estado')
                            ->options([
                                'active' => 'Activo',
                                'inactive' => 'Inactivo',
                            ])
                            ->default('active')
                            ->required()
                            ->helperText('Usuarios inactivos no pueden acceder al sistema'),
                    ])
                    ->columns(2),

                Section::make('Seguridad')
                    ->description('Contraseña de acceso')
                    ->schema([
                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->required(fn (string $context): bool => $context === 'create')
                            ->dehydrated(fn ($state) => filled($state))
                            ->minLength(8)
                            ->helperText('Mínimo 8 caracteres. Dejar en blanco para mantener la actual.')
                            ->revealable(),
                        
                        TextInput::make('password_confirmation')
                            ->label('Confirmar Contraseña')
                            ->password()
                            ->same('password')
                            ->dehydrated(false)
                            ->required(fn (string $context): bool => $context === 'create')
                            ->revealable(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
