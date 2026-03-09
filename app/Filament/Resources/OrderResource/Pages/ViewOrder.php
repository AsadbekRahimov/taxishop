<?php

declare(strict_types=1);

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ViewRecord;
use App\Models\Order;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('changeStatus')
                ->label('Сменить статус')
                ->icon('heroicon-o-arrow-path')
                ->form([
                    Forms\Components\Select::make('status')
                        ->label('Новый статус')
                        ->options([
                            'new' => 'Новый',
                            'paid' => 'Оплачен',
                            'delivered' => 'Доставлен',
                            'cancelled' => 'Отменён',
                        ])
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->record->update(['status' => $data['status']]);
                    $this->refreshFormData(['status']);
                }),
        ];
    }
}
