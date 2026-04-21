<?php

namespace App\Filament\Resources\Suppliers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('address')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn () => auth()->user()->role === 'admin'),
                DeleteAction::make()
                    ->visible(fn () => auth()->user()->role === 'admin'),
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

                            $headers = ['name', 'phone', 'email', 'status', 'address'];

                            $col = 'A';
                            foreach ($headers as $header) {
                                $sheet->setCellValue($col . '1', $header);
                                $col++;
                            }

                            $sheet->getStyle('A1:E1')->getFont()->setBold(true);
                            $sheet->getStyle('A1:E1')->getFill()
                                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('4F46E5');
                            $sheet->getStyle('A1:E1')->getFont()->getColor()->setRGB('FFFFFF');

                            foreach (range('A', 'E') as $col) {
                                $sheet->getColumnDimension($col)->setAutoSize(true);
                            }

                            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
                            $filePath = storage_path('app/private/plantilla-proveedores.xlsx');
                            $writer->save($filePath);

                            return response()->download($filePath, 'plantilla-proveedores.xlsx', [
                                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])->deleteFileAfterSend(true);
                        }),
                    \Filament\Actions\Action::make('exportReport')
                        ->label('Reporte PDF')
                        ->icon('heroicon-o-document-chart-bar')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Exportar Reporte PDF')
                        ->modalDescription('¿Deseas descargar el reporte completo de proveedores en PDF?')
                        ->modalSubmitActionLabel('Descargar PDF')
                        ->action(function () {
                            $suppliers = \App\Models\Supplier::all();
                            $farmacia = \App\Models\Setting::get('pharmacy_name', config('app.name'));

                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.suppliers', [
                                'suppliers' => $suppliers,
                                'farmacia' => $farmacia,
                            ])->setPaper('a4', 'landscape');

                            return response()->streamDownload(
                                fn() => print($pdf->output()),
                                'reporte-proveedores-' . now()->format('Y-m-d') . '.pdf'
                            );
                        }),
                    \Filament\Actions\ExportAction::make()
                        ->exporter(\App\Filament\Exports\SupplierExporter::class)
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
                                new \App\Imports\SuppliersImport,
                                \Illuminate\Support\Facades\Storage::disk('public')->path($data['file'])
                            );

                            \Filament\Notifications\Notification::make()
                                ->title('Importación exitosa')
                                ->body('Los proveedores fueron importados correctamente')
                                ->success()
                                ->send();
                        })
                        ->modalHeading('Importar Proveedores desde Excel')
                        ->modalSubmitActionLabel('Importar')
                        ->successNotificationTitle('Proveedores agregados'),
                ])
                    ->label('Acciones')
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->button()
                    ->color('primary'),
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->visible(fn () => auth()->user()->role === 'admin')
                    ->successNotificationTitle('Proveedor agregado'),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])
                    ->visible(fn () => auth()->user()->role === 'admin'),
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
