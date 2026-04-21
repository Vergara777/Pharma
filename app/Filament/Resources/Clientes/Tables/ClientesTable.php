<?php

namespace App\Filament\Resources\Clientes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ClientesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\Layout\Split::make([
                    \Filament\Tables\Columns\Layout\Stack::make([
                        TextColumn::make('name')
                            ->label('Nombre')
                            ->weight('bold')
                            ->searchable()
                            ->sortable(),
                        TextColumn::make('document')
                            ->label('Documento')
                            ->icon('heroicon-m-identification')
                            ->color('gray')
                            ->searchable()
                            ->placeholder('Sin documento'),
                    ])->space(1),

                    \Filament\Tables\Columns\Layout\Stack::make([
                        TextColumn::make('phone')
                            ->label('Teléfono')
                            ->icon('heroicon-m-phone')
                            ->searchable()
                            ->placeholder('Sin teléfono'),
                        TextColumn::make('email')
                            ->label('Email')
                            ->icon('heroicon-m-envelope')
                            ->searchable()
                            ->placeholder('Sin correo'),
                    ])->space(1),

                    \Filament\Tables\Columns\Layout\Stack::make([
                        TextColumn::make('facturas_count')
                            ->label('Facturas')
                            ->counts('facturas')
                            ->badge()
                            ->color('success')
                            ->sortable(),
                        TextColumn::make('ventas_count')
                            ->label('Ventas')
                            ->counts('ventas')
                            ->badge()
                            ->color('info')
                            ->sortable(),
                    ])->space(1),

                    \Filament\Tables\Columns\Layout\Stack::make([
                        IconColumn::make('is_active')
                            ->label('Activo')
                            ->boolean(),
                        TextColumn::make('address')
                            ->label('Dirección')
                            ->icon('heroicon-m-map-pin')
                            ->color('gray')
                            ->searchable()
                            ->placeholder('Sin dirección'),
                    ])->space(1),
                ])->from('md'),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Estado')
                    ->options([
                        1 => 'Activo',
                        0 => 'Inactivo',
                    ]),
                \Filament\Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('desde')
                            ->label('Desde')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        \Filament\Forms\Components\DatePicker::make('hasta')
                            ->label('Hasta')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['desde'], fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['hasta'], fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['desde'] && !$data['hasta']) {
                            return null;
                        }
                        
                        $desde = $data['desde'] ? \Carbon\Carbon::parse($data['desde'])->format('d/m/Y') : '';
                        $hasta = $data['hasta'] ? \Carbon\Carbon::parse($data['hasta'])->format('d/m/Y') : '';
                        
                        if ($desde && $hasta) {
                            return "Fecha: {$desde} - {$hasta}";
                        } elseif ($desde) {
                            return "Desde: {$desde}";
                        } else {
                            return "Hasta: {$hasta}";
                        }
                    }),
                \Filament\Tables\Filters\Filter::make('today')
                    ->label('Solo Hoy')
                    ->query(fn ($query) => $query->whereDate('created_at', today()))
                    ->toggle()
                    ->default(true),
                \Filament\Tables\Filters\Filter::make('this_week')
                    ->label('Esta Semana')
                    ->query(fn ($query) => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
                    ->toggle(),
                \Filament\Tables\Filters\Filter::make('this_month')
                    ->label('Este Mes')
                    ->query(fn ($query) => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year))
                    ->toggle(),
                \Filament\Tables\Filters\Filter::make('all')
                    ->label('Ver Todos')
                    ->query(fn ($query) => $query)
                    ->toggle(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record): string => route('filament.admin.resources.clientes.view', ['record' => $record]))
                    ->color('gray'),
                EditAction::make()
                    ->visible(fn () => auth()->user()->role === 'admin')
                    ->color('warning'),
            ])
            ->toolbarActions([
                \Filament\Actions\ActionGroup::make([
                    \Filament\Actions\Action::make('downloadTemplate')
                        ->label('Descargar Plantilla')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('gray')
                        ->action(function () {
                            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
                            $sheet = $spreadsheet->getActiveSheet();

                            $headers = ['name', 'document', 'email', 'phone', 'address', 'is_active'];

                            $col = 'A';
                            foreach ($headers as $header) {
                                $sheet->setCellValue($col . '1', $header);
                                $col++;
                            }

                            $sheet->getStyle('A1:F1')->getFont()->setBold(true);
                            $sheet->getStyle('A1:F1')->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('4F46E5');
                            $sheet->getStyle('A1:F1')->getFont()->getColor()->setRGB('FFFFFF');

                            foreach (range('A', 'F') as $col) {
                                $sheet->getColumnDimension($col)->setAutoSize(true);
                            }

                            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                            $filePath = storage_path('app/private/plantilla-clientes.xlsx');
                            $writer->save($filePath);

                            return response()->download($filePath, 'plantilla-clientes.xlsx', [
                                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])->deleteFileAfterSend(true);
                        }),
                    \Filament\Actions\Action::make('exportReport')
                        ->label('Reporte PDF')
                        ->icon('heroicon-o-document-chart-bar')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Exportar Reporte PDF')
                        ->modalDescription('¿Deseas descargar el reporte completo de clientes en PDF?')
                        ->modalSubmitActionLabel('Descargar PDF')
                        ->action(function () {
                            $clientes = \App\Models\Cliente::all();
                            $farmacia = \App\Models\Setting::get('pharmacy_name', config('app.name'));

                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.clientes', [
                                'clientes' => $clientes,
                                'farmacia' => $farmacia,
                            ])->setPaper('a4', 'landscape');

                            return response()->streamDownload(
                                fn() => print($pdf->output()),
                                'reporte-clientes-' . now()->format('Y-m-d') . '.pdf'
                            );
                        }),
                    \Filament\Actions\ExportAction::make()
                        ->exporter(\App\Filament\Exports\ClienteExporter::class)
                        ->icon('heroicon-o-arrow-down-tray')
                        ->label('Exportar Excel')
                        ->columnMapping(false),
                    \Filament\Actions\Action::make('importExcel')
                        ->label('Importar Excel')
                        ->icon('heroicon-o-arrow-up-tray')
                        ->color('success')
                        ->form([
                            \Filament\Forms\Components\FileUpload::make('file')
                                ->label('Archivo Excel')
                                ->disk('public')
                                ->directory('imports')
                                ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                                ->required(),
                        ])
                        ->action(function (array $data) {
                            \Maatwebsite\Excel\Facades\Excel::import(
                                new \App\Imports\ClientesImport,
                                \Illuminate\Support\Facades\Storage::disk('public')->path($data['file'])
                            );

                            \Filament\Notifications\Notification::make()
                                ->title('Importación exitosa')
                                ->body('Los clientes fueron importados correctamente')
                                ->success()
                                ->send();
                        })
                        ->modalHeading('Importar Clientes desde Excel')
                        ->modalSubmitActionLabel('Importar')
                        ->successNotificationTitle('Clientes agregados'),
                ])
                    ->label('Acciones')
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->button()
                    ->color('primary'),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->persistFiltersInSession()
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistColumnSearchesInSession()
            ->paginationPageOptions([10, 25, 50, 100])
            ->defaultPaginationPageOption(25);
    }
}
