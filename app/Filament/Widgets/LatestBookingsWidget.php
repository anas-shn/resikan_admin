<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Filament\Admin\Resources\BookingResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestBookingsWidget extends BaseWidget
{
    protected static ?string $heading = 'Booking Terbaru';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::query()
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('booking_number')
                    ->label('No. Booking')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('user.fullname')
                    ->label('Pelanggan')
                    ->searchable()
                    ->limit(20),

                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Jadwal')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'in_progress' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (Booking $record): string => BookingResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye')
                    ->label('Lihat'),
            ]);
    }
}
