<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Booking';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Booking';
    protected static ?string $pluralModelLabel = 'Booking';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Booking')
                    ->schema([
                        Forms\Components\TextInput::make('booking_number')
                            ->label('Nomor Booking')
                            ->disabled()
                            ->default(fn () => 'BK-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6)))
                            ->dehydrated()
                            ->unique(ignoreRecord: true),

                        Forms\Components\Select::make('user_id')
                            ->label('Pelanggan')
                            ->relationship('user', 'fullname')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('cleaner_id')
                            ->label('Petugas')
                            ->relationship('cleaner', 'fullname')
                            ->searchable()
                            ->preload()
                            ->helperText('Kosongkan jika belum ditentukan'),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Pending',
                                'confirmed' => 'Confirmed',
                                'in_progress' => 'In Progress',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])
                            ->required()
                            ->default('pending')
                            ->native(false),
                    ])->columns(2),

                Forms\Components\Section::make('Jadwal & Lokasi')
                    ->schema([
                        Forms\Components\DateTimePicker::make('scheduled_at')
                            ->label('Jadwal')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->seconds(false),

                        Forms\Components\TextInput::make('duration_minutes')
                            ->label('Durasi')
                            ->numeric()
                            ->suffix('menit')
                            ->default(60),

                        Forms\Components\Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('location.lat')
                                    ->label('Latitude')
                                    ->numeric()
                                    ->step(0.000001),

                                Forms\Components\TextInput::make('location.lng')
                                    ->label('Longitude')
                                    ->numeric()
                                    ->step(0.000001),
                            ]),
                    ]),

                Forms\Components\Section::make('Detail Layanan')
                    ->schema([
                        Forms\Components\Repeater::make('bookingItems')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('service_id')
                                    ->label('Layanan')
                                    ->relationship('service', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $service = Service::find($state);
                                        if ($service) {
                                            $set('price', $service->base_price);
                                        }
                                    }),

                                Forms\Components\TextInput::make('quantity')
                                    ->label('Jumlah')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->minValue(1),

                                Forms\Components\TextInput::make('price')
                                    ->label('Harga')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required(),

                                Forms\Components\Textarea::make('notes')
                                    ->label('Catatan')
                                    ->rows(2),
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                            ->addActionLabel('Tambah Layanan')
                            ->reorderable()
                            ->collapsible()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('total_price')
                            ->label('Total Harga')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->default(0),
                    ]),

                Forms\Components\Section::make('Tambahan')
                    ->schema([
                        Forms\Components\KeyValue::make('extras')
                            ->label('Informasi Tambahan')
                            ->keyLabel('Keterangan')
                            ->valueLabel('Nilai')
                            ->reorderable()
                            ->columnSpanFull(),
                    ])
                    ->collapsed(),

                Forms\Components\Section::make('Informasi Sistem')
                    ->schema([
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Dibuat Pada')
                            ->disabled()
                            ->displayFormat('d/m/Y H:i'),

                        Forms\Components\DateTimePicker::make('updated_at')
                            ->label('Diperbarui Pada')
                            ->disabled()
                            ->displayFormat('d/m/Y H:i'),
                    ])
                    ->columns(2)
                    ->collapsed()
                    ->hiddenOn('create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('booking_number')
                    ->label('No. Booking')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('user.fullname')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('cleaner.fullname')
                    ->label('Petugas')
                    ->searchable()
                    ->sortable()
                    ->default('-')
                    ->placeholder('Belum ditentukan'),

                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Jadwal')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->description(fn ($record) => $record->duration_minutes . ' menit'),

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
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'confirmed' => 'heroicon-o-check-circle',
                        'in_progress' => 'heroicon-o-arrow-path',
                        'completed' => 'heroicon-o-check-badge',
                        'cancelled' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->multiple(),

                Tables\Filters\Filter::make('scheduled_at')
                    ->form([
                        Forms\Components\DatePicker::make('scheduled_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('scheduled_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['scheduled_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scheduled_at', '>=', $date),
                            )
                            ->when(
                                $data['scheduled_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('scheduled_at', '<=', $date),
                            );
                    }),

                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Pelanggan')
                    ->relationship('user', 'fullname')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('cleaner_id')
                    ->label('Petugas')
                    ->relationship('cleaner', 'fullname')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('assign_cleaner')
                        ->label('Assign Petugas')
                        ->icon('heroicon-o-user-plus')
                        ->form([
                            Forms\Components\Select::make('cleaner_id')
                                ->label('Pilih Petugas')
                                ->relationship('cleaner', 'fullname')
                                ->searchable()
                                ->preload()
                                ->required(),
                        ])
                        ->action(function ($record, array $data) {
                            $record->update(['cleaner_id' => $data['cleaner_id']]);
                            Notification::make()
                                ->title('Petugas berhasil ditugaskan')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('change_status')
                        ->label('Ubah Status')
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Status Baru')
                                ->options([
                                    'pending' => 'Pending',
                                    'confirmed' => 'Confirmed',
                                    'in_progress' => 'In Progress',
                                    'completed' => 'Completed',
                                    'cancelled' => 'Cancelled',
                                ])
                                ->required(),
                        ])
                        ->action(function ($record, array $data) {
                            $record->update(['status' => $data['status']]);
                            Notification::make()
                                ->title('Status berhasil diubah')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteAction::make()
                        ->requiresConfirmation(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('change_status')
                        ->label('Ubah Status')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Status Baru')
                                ->options([
                                    'pending' => 'Pending',
                                    'confirmed' => 'Confirmed',
                                    'in_progress' => 'In Progress',
                                    'completed' => 'Completed',
                                    'cancelled' => 'Cancelled',
                                ])
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each->update(['status' => $data['status']]);
                            Notification::make()
                                ->title('Status booking berhasil diubah')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'view' => Pages\ViewBooking::route('/{record}'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['booking_number', 'user.fullname'];
    }
}
