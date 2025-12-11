# Filament Resources - Detailed Structure

## 📋 Overview
Dokumen ini berisi struktur detail untuk setiap Filament Resource yang akan diimplementasikan.

---

## 1. User Resource

### File: `app/Filament/Resources/UserResource.php`

#### Form Schema
```php
Forms\Components\Section::make('Informasi Pengguna')
    ->schema([
        Forms\Components\TextInput::make('fullname')
            ->label('Nama Lengkap')
            ->required()
            ->maxLength(200),
            
        Forms\Components\TextInput::make('email')
            ->label('Email')
            ->email()
            ->unique(ignoreRecord: true)
            ->maxLength(200),
            
        Forms\Components\TextInput::make('phone')
            ->label('Nomor Telepon')
            ->tel()
            ->unique(ignoreRecord: true)
            ->maxLength(30),
            
        Forms\Components\Textarea::make('address')
            ->label('Alamat')
            ->rows(3)
            ->columnSpanFull(),
            
        Forms\Components\KeyValue::make('metadata')
            ->label('Metadata')
            ->keyLabel('Key')
            ->valueLabel('Value')
            ->columnSpanFull(),
    ]),

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
```

#### Table Schema
```php
Tables\Columns\TextColumn::make('fullname')
    ->label('Nama Lengkap')
    ->searchable()
    ->sortable(),

Tables\Columns\TextColumn::make('email')
    ->label('Email')
    ->searchable()
    ->sortable()
    ->copyable()
    ->icon('heroicon-o-envelope'),

Tables\Columns\TextColumn::make('phone')
    ->label('Telepon')
    ->searchable()
    ->copyable()
    ->icon('heroicon-o-phone'),

Tables\Columns\TextColumn::make('bookings_count')
    ->label('Total Booking')
    ->counts('bookings')
    ->sortable()
    ->badge()
    ->color('success'),

Tables\Columns\TextColumn::make('created_at')
    ->label('Terdaftar')
    ->dateTime('d/m/Y')
    ->sortable()
    ->toggleable(),
```

#### Filters
```php
Tables\Filters\Filter::make('created_at')
    ->form([
        Forms\Components\DatePicker::make('created_from')
            ->label('Dari Tanggal'),
        Forms\Components\DatePicker::make('created_until')
            ->label('Sampai Tanggal'),
    ])
    ->query(function (Builder $query, array $data): Builder {
        return $query
            ->when(
                $data['created_from'],
                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
            )
            ->when(
                $data['created_until'],
                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
            );
    }),

Tables\Filters\SelectFilter::make('has_bookings')
    ->label('Status Booking')
    ->options([
        'with_bookings' => 'Pernah Booking',
        'without_bookings' => 'Belum Pernah Booking',
    ])
    ->query(function (Builder $query, array $data): Builder {
        return $query
            ->when(
                $data['value'] === 'with_bookings',
                fn (Builder $query): Builder => $query->has('bookings')
            )
            ->when(
                $data['value'] === 'without_bookings',
                fn (Builder $query): Builder => $query->doesntHave('bookings')
            );
    }),
```

#### Actions
```php
Tables\Actions\ViewAction::make(),
Tables\Actions\EditAction::make(),
Tables\Actions\DeleteAction::make()
    ->requiresConfirmation(),
```

#### Bulk Actions
```php
Tables\Actions\BulkActionGroup::make([
    Tables\Actions\DeleteBulkAction::make()
        ->requiresConfirmation(),
    Tables\Actions\ExportBulkAction::make()
        ->label('Export ke Excel'),
]),
```

---

## 2. Service Resource

### File: `app/Filament/Resources/ServiceResource.php`

#### Form Schema
```php
Forms\Components\Section::make('Informasi Layanan')
    ->schema([
        Forms\Components\TextInput::make('code')
            ->label('Kode Layanan')
            ->required()
            ->unique(ignoreRecord: true)
            ->maxLength(50)
            ->placeholder('Contoh: SVC001'),
            
        Forms\Components\TextInput::make('name')
            ->label('Nama Layanan')
            ->required()
            ->maxLength(200),
            
        Forms\Components\RichEditor::make('description')
            ->label('Deskripsi')
            ->toolbarButtons([
                'bold',
                'bulletList',
                'italic',
                'orderedList',
                'redo',
                'undo',
            ])
            ->columnSpanFull(),
    ]),

Forms\Components\Section::make('Harga & Durasi')
    ->schema([
        Forms\Components\TextInput::make('base_price')
            ->label('Harga Dasar')
            ->required()
            ->numeric()
            ->prefix('Rp')
            ->step(1000)
            ->default(0),
            
        Forms\Components\TextInput::make('default_duration_minutes')
            ->label('Durasi Default')
            ->required()
            ->numeric()
            ->suffix('menit')
            ->step(15)
            ->default(60),
            
        Forms\Components\Toggle::make('active')
            ->label('Aktif')
            ->default(true)
            ->helperText('Layanan yang aktif akan muncul di aplikasi'),
    ])
    ->columns(3),

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
```

#### Table Schema
```php
Tables\Columns\TextColumn::make('code')
    ->label('Kode')
    ->searchable()
    ->sortable()
    ->copyable()
    ->badge()
    ->color('gray'),

Tables\Columns\TextColumn::make('name')
    ->label('Nama Layanan')
    ->searchable()
    ->sortable()
    ->weight('bold'),

Tables\Columns\TextColumn::make('base_price')
    ->label('Harga')
    ->money('IDR')
    ->sortable(),

Tables\Columns\TextColumn::make('default_duration_minutes')
    ->label('Durasi')
    ->formatStateUsing(fn ($state) => $state . ' menit')
    ->sortable(),

Tables\Columns\IconColumn::make('active')
    ->label('Status')
    ->boolean()
    ->trueIcon('heroicon-o-check-circle')
    ->falseIcon('heroicon-o-x-circle')
    ->trueColor('success')
    ->falseColor('danger')
    ->sortable(),

Tables\Columns\TextColumn::make('booking_items_count')
    ->label('Total Pesanan')
    ->counts('bookingItems')
    ->sortable()
    ->badge()
    ->color('info'),

Tables\Columns\TextColumn::make('created_at')
    ->label('Dibuat')
    ->dateTime('d/m/Y')
    ->sortable()
    ->toggleable(isToggledHiddenByDefault: true),
```

#### Filters
```php
Tables\Filters\TernaryFilter::make('active')
    ->label('Status Layanan')
    ->placeholder('Semua')
    ->trueLabel('Aktif')
    ->falseLabel('Tidak Aktif'),

Tables\Filters\Filter::make('base_price')
    ->form([
        Forms\Components\TextInput::make('price_from')
            ->label('Harga Dari')
            ->numeric()
            ->prefix('Rp'),
        Forms\Components\TextInput::make('price_until')
            ->label('Harga Sampai')
            ->numeric()
            ->prefix('Rp'),
    ])
    ->query(function (Builder $query, array $data): Builder {
        return $query
            ->when(
                $data['price_from'],
                fn (Builder $query, $price): Builder => $query->where('base_price', '>=', $price),
            )
            ->when(
                $data['price_until'],
                fn (Builder $query, $price): Builder => $query->where('base_price', '<=', $price),
            );
    }),
```

---

## 3. Booking Resource

### File: `app/Filament/Resources/BookingResource.php`

#### Form Schema
```php
Forms\Components\Section::make('Informasi Booking')
    ->schema([
        Forms\Components\TextInput::make('booking_number')
            ->label('Nomor Booking')
            ->disabled()
            ->default(fn () => 'BK-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6)))
            ->unique(ignoreRecord: true),
            
        Forms\Components\Select::make('user_id')
            ->label('Pelanggan')
            ->relationship('user', 'fullname')
            ->searchable()
            ->preload()
            ->required()
            ->createOptionForm([
                Forms\Components\TextInput::make('fullname')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email(),
                Forms\Components\TextInput::make('phone')
                    ->tel(),
            ]),
            
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
    ])
    ->columns(2),

Forms\Components\Section::make('Jadwal & Lokasi')
    ->schema([
        Forms\Components\DateTimePicker::make('scheduled_at')
            ->label('Jadwal')
            ->required()
            ->native(false)
            ->displayFormat('d/m/Y H:i')
            ->seconds(false)
            ->minDate(now()),
            
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
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $service = \App\Models\Service::find($state);
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
            ->default(0)
            ->disabled()
            ->dehydrated(),
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
```

#### Table Schema
```php
Tables\Columns\TextColumn::make('booking_number')
    ->label('No. Booking')
    ->searchable()
    ->sortable()
    ->copyable()
    ->weight('bold'),

Tables\Columns\TextColumn::make('user.fullname')
    ->label('Pelanggan')
    ->searchable()
    ->sortable()
    ->url(fn ($record) => UserResource::getUrl('view', ['record' => $record->user]))
    ->openUrlInNewTab(),

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

Tables\Columns\BadgeColumn::make('status')
    ->label('Status')
    ->colors([
        'warning' => 'pending',
        'info' => 'confirmed',
        'primary' => 'in_progress',
        'success' => 'completed',
        'danger' => 'cancelled',
    ])
    ->icons([
        'heroicon-o-clock' => 'pending',
        'heroicon-o-check-circle' => 'confirmed',
        'heroicon-o-arrow-path' => 'in_progress',
        'heroicon-o-check-badge' => 'completed',
        'heroicon-o-x-circle' => 'cancelled',
    ])
    ->sortable(),

Tables\Columns\TextColumn::make('created_at')
    ->label('Dibuat')
    ->dateTime('d/m/Y H:i')
    ->sortable()
    ->toggleable(isToggledHiddenByDefault: true),
```

#### Filters
```php
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

Tables\Filters\Filter::make('total_price')
    ->form([
        Forms\Components\TextInput::make('price_from')
            ->label('Dari')
            ->numeric()
            ->prefix('Rp'),
        Forms\Components\TextInput::make('price_until')
            ->label('Sampai')
            ->numeric()
            ->prefix('Rp'),
    ])
    ->query(function (Builder $query, array $data): Builder {
        return $query
            ->when(
                $data['price_from'],
                fn (Builder $query, $price): Builder => $query->where('total_price', '>=', $price),
            )
            ->when(
                $data['price_until'],
                fn (Builder $query, $price): Builder => $query->where('total_price', '<=', $price),
            );
    }),
```

#### Actions
```php
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
```

#### Bulk Actions
```php
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
    
    Tables\Actions\ExportBulkAction::make()
        ->label('Export ke Excel'),
]),
```

#### Relations
```php
// BookingResource/RelationManagers/BookingItemsRelationManager.php
public function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('service.name')
                ->label('Layanan'),
            Tables\Columns\TextColumn::make('quantity')
                ->label('Jumlah'),
            Tables\Columns\TextColumn::make('price')
                ->label('Harga')
                ->money('IDR'),
            Tables\Columns\TextColumn::make('subtotal')
                ->label('Subtotal')
                ->money('IDR')
                ->state(fn ($record) => $record->quantity * $record->price),
        ])
        ->headerActions([
            Tables\Actions\CreateAction::make(),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
}

// BookingResource/RelationManagers/PaymentsRelationManager.php
public function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('amount')
                ->label('Jumlah')
                ->money('IDR'),
            Tables\Columns\TextColumn::make('method')
                ->label('Metode'),
            Tables\Columns\BadgeColumn::make('status')
                ->label('Status')
                ->colors([
                    'warning' => 'pending',
                    'success' => 'paid',
                    'danger' => 'failed',
                ]),
            Tables\Columns\TextColumn::make('paid_at')
                ->label('Tanggal Bayar')
                ->dateTime('d/m/Y H:i'),
        ])
        ->headerActions([
            Tables\Actions\CreateAction::make(),
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
        ]);
}
```

---

## Navigation Configuration

### File: `app/Filament/Resources/*/Resource.php`

```php
// UserResource
protected static ?string $navigationIcon = 'heroicon-o-users';
protected static ?string $navigationLabel = 'Pengguna';
protected static ?string $navigationGroup = 'Manajemen';
protected static ?int $navigationSort = 1;

// ServiceResource
protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
protected static ?string $navigationLabel = 'Layanan';
protected static ?string $navigationGroup = 'Manajemen';
protected static ?int $navigationSort = 2;

// BookingResource
protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
protected static ?string $navigationLabel = 'Booking';
protected static ?string $navigationGroup = 'Transaksi';
protected static ?int $navigationSort = 1;
protected static ?string $navigationBadge = fn () => Booking::where('status', 'pending')->count();
protected static ?string $navigationBadgeColor = 'warning';
```

---

## Global Search Configuration

```php
// UserResource
public static function getGloballySearchableAttributes(): array
{
    return ['fullname', 'email', 'phone'];
}

public static function getGlobalSearchResultTitle(Model $record): string
{
    return $record->fullname;
}

public static function getGlobalSearchResultDetails(Model $record): array
{
    return [
        'Email' => $record->email,
        'Phone' => $record->phone,
    ];
}

// BookingResource
public static function getGloballySearchableAttributes(): array
{
    return ['booking_number', 'user.fullname'];
}

public static function getGlobalSearchResultTitle(Model $record): string
{
    return $record->booking_number;
}

public static function getGlobalSearchResultDetails(Model $record): array
{
    return [
        'Pelanggan' => $record->user->fullname,
        'Status' => ucfirst($record->status),
        'Total' => 'Rp ' . number_format($record->total_price, 0, ',', '.'),
    ];
}
```

---

## Model Relationships Required

### User Model
```php
public function bookings()
{
    return $this->hasMany(Booking::class);
}

public function subscriptions()
{
    return $this->hasMany(Subscription::class);
}

public function ratings()
{
    return $this->hasMany(Rating::class);
}
```

### Booking Model
```php
public function user()
{
    return $this->belongsTo(User::class);
}

public function cleaner()
{
    return $this->belongsTo(Cleaner::class);
}

public function bookingItems()
{
    return $this->hasMany(BookingItem::class);
}

public function payments()
{
    return $this->hasMany(Payment::class);
}

public function rating()
{
    return $this->hasOne(Rating::class);
}
```

### BookingItem Model
```php
public function booking()
{
    return $this->belongsTo(Booking::class);
}

public function service()
{
    return $this->belongsTo(Service::class);
}
```

### Service Model
```php
public function bookingItems()
{
    return $this->hasMany(BookingItem::class);
}
```

---

## Casts & Attributes

### User Model
```php
protected $fillable = [
    'fullname',
    'email',
    'phone',
    'address',
    'metadata',
];

protected $casts = [
    'metadata' => 'array',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
];
```

### Booking Model
```php
protected $fillable = [
    'booking_number',
    'user_id',
    'cleaner_id',
    'scheduled_at',
    'duration_minutes',
    'total_price',
    'status',
    'address',
    'location',
    'extras',
];

protected $casts = [
    'scheduled_at' => 'datetime',
    'location' => 'array',
    'extras' => 'array',
    'total_price' => 'decimal:2',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
];
```

### Service Model
```php
protected $fillable = [
    'code',
    'name',
    'description',
    'base_price',
    'default_duration_minutes',
    'active',
];

protected $casts = [
    'base_price' => 'decimal:2',
    'active' => 'boolean',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
];
```

---

**Version:** 1.0  
**Last Updated:** 2024  
**Status:** Ready for Implementation ✅