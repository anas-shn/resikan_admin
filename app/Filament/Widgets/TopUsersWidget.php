<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Filament\Admin\Resources\UserResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class TopUsersWidget extends BaseWidget
{
    protected static ?string $heading = 'Pengguna Teratas';
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->select('users.*')
                    ->selectRaw('COUNT(bookings.id) as bookings_count')
                    ->selectRaw('COALESCE(SUM(bookings.total_price), 0) as total_spent')
                    ->leftJoin('bookings', 'users.id', '=', 'bookings.user_id')
                    ->groupBy('users.id')
                    ->orderBy('bookings_count', 'desc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('fullname')
                    ->label('Nama')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-phone'),

                Tables\Columns\TextColumn::make('bookings_count')
                    ->label('Total Booking')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_spent')
                    ->label('Total Pengeluaran')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (User $record): string => UserResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye')
                    ->label('Lihat'),
            ]);
    }
}
