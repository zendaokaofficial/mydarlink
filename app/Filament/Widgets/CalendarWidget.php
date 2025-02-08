<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Forms\Form;
use App\Models\RencanaKinerja;
use Filament\Facades\Filament;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Saade\FilamentFullCalendar\Actions\CreateAction;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    // protected static string $view = 'filament.widgets.calendar-widget';

    use InteractsWithPageFilters;

    public Model | string | null $model = RencanaKinerja::class;

    public function fetchEvents(array $fetchInfo): array
    {
        $kategoriColors = [
            'Rapat' => '#295F98',
            'Supervisi/Pengawasan' => '#F6995C',
            'Zoom' => '#FCDC94',
            'Pelatihan/Briefing' => '#A5DD9B',
            'SPPD' => '#FF90BC',
            'Cuti' => '#FF8787',
            'Deadline' => '#5C7285',
            'Lainnya' => '#8D77AB',
        ];

        // Log untuk debugging
        Log::info('Fetching events...', ['filters' => $this->filters, 'fetchInfo' => $fetchInfo]);

        // Query untuk mengambil event dengan filter
        $query = RencanaKinerja::query()
            ->where('start_at', '>=', $fetchInfo['start'])
            ->where('end_at', '<=', $fetchInfo['end']);

        // Filter berdasarkan User jika ada
        if (!empty($this->filters['rencana_kinerja_user']['user_id'])) {
            $query->whereHas('users', function ($query) {
                $query->whereIn('users.id', $this->filters['rencana_kinerja_user']['user_id']);
            });
        }

        // Filter berdasarkan Tempat jika ada
        if (!empty($this->filters['tempat'])) {
            $query->whereIn('tempat', $this->filters['tempat']);
        }

        // Filter berdasarkan Kategori jika ada
        if (!empty($this->filters['kategori'])) {
            $query->whereIn('kategori', $this->filters['kategori']);
        }

        // Ambil data dan format untuk FullCalendar
        return $query
        ->get()
        ->map(
            fn (RencanaKinerja $event) => [
            'id' => $event->id,
            'title' => "{$event->rencana_kinerja}",
            'start' => $event->start_at,
            'end' => $event->end_at,
            'color' => $kategoriColors[$event->kategori] ?? '#000000',

        ])->all();
    }

    public function getFormSchema(): array
    {
        return [
            Section::make('Rencana Kinerja')->schema([
                Select::make('proyek_id') // Relasi ke Proyek
                    ->label('Proyek')
                    ->relationship('proyek', 'nama_proyek')
                    ->required()
                    ->placeholder('Pilih Proyek')
                    ->searchable(),
                TextInput::make('rencana_kinerja')
                    ->label('Rencana Kinerja')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->required()
                    ->maxLength(255)
                    ->label('Deskripsi'),
                    DateTimePicker::make('start_at')
                    ->required()
                    ->default(now())
                    ->label('Waktu Mulai')
                    ->reactive()
                    ->seconds(false)
                    ->timezone('Asia/Makassar'),
                DateTimePicker::make('end_at')
                    ->required()
                    ->label('Waktu Selesai')
                    ->reactive()
                    ->rules([
                        fn ($get) => function (string $attribute, $value, $fail) use ($get) {
                            Log::info('Validasi Waktu', [
                                'start_at' => $get('start_at'),
                                'end_at' => $value
                            ]);

                            if ($get('start_at') && $value <= $get('start_at')) {
                                $fail('Waktu selesai harus lebih besar dari waktu mulai.');
                            }
                        },
                    ])
                    ->validationMessages([
                        'after' => 'Waktu selesai harus lebih besar dari waktu mulai.',
                    ])
                    ->seconds(false)
                    ->timezone('Asia/Makassar'),
                Select::make('tempat')
                    ->options([
                        'Ruang Rapat' => 'Ruang Rapat',
                        'PST' => 'PST',
                        'Kantor' => 'Kantor',
                        'Luar Kantor' => 'Luar Kantor',
                        'Online' => 'Online',
                        'Lainnya' => 'Lainnya',
                    ])
                    ->required()
                    ->reactive()
                    ->placeholder('Pilih Tempat'),
                TextInput::make('tempat_lainnya')
                    ->required(fn ($get) => $get('tempat') === 'Lainnya')
                    ->visible(fn ($get) => $get('tempat') === 'Lainnya')
                    ->placeholder('Isi Tempat Lainnya'),
                CheckboxList::make('users')
                    ->label('Nama Pegawai')
                    ->relationship('users', 'name')
                    ->options(function () {
                        return \App\Models\User::where('role', '!=', 'Admin')->pluck('name', 'id');
                    })
                    ->columns(3),
                ]),
                Section::make()->schema([
                    Select::make('kategori')
                        ->options([
                            'Rapat' => 'Rapat',
                            'Supervisi/Pengawasan' => 'Supervisi/Pengawasan',
                            'Zoom' => 'Zoom',
                            'Pelatihan/Briefing' => 'Pelatihan/Briefing',
                            'SPPD' => 'SPPD',
                            'Cuti' => 'Cuti',
                            'Deadline' => 'Deadline',
                            'Lainnya' => 'Lainnya',
                        ])
                        ->required()
                        ->placeholder('Pilih Kategori'),
                    TextInput::make('target')
                        ->required()
                        ->placeholder('Target'),
                    Select::make('satuan')
                        ->options([
                            '%' => '%',
                            'Kegiatan' => 'Kegiatan',
                            'Ruta' => 'Ruta',
                            'Usaha' => 'Usaha',
                            'Poin' => 'Poin',
                            'Pemda' => 'Pemda',
                            'Satker' => 'Satker',
                        ])
                        ->required()
                        ->placeholder('Pilih Satuan'),
                        TextInput::make('realisasi')
                        ->required()
                        ->placeholder('Realisasi')
                        ->rules([
                            fn ($get) => function (string $attribute, $value, $fail) use ($get) {
                                Log::info('Validasi Realisasi', [
                                    'target' => $get('target'),
                                    'realisasi' => $value
                                ]);

                                if ($get('target') && $value > $get('target')) {
                                    $fail('Realisasi tidak boleh lebih besar dari target.');
                                }
                            },
                        ])
                        ->validationMessages([
                            'max' => 'Realisasi tidak boleh lebih besar dari target.',
                        ]),

                ]),
                Section::make()->schema([
                    TextInput::make('daftar_hadir')
                        ->placeholder('Daftar Hadir'),
                    TextInput::make('rekap_daftar_hadir')
                        ->placeholder('Rekap Daftar Hadir'),
                    TextInput::make('link_materi')
                        ->placeholder('Link Materi'),
                    TextInput::make('notulensi')
                        ->placeholder('Notulensi'),
                ]),
        ];
    }

    protected function modalActions(): array
    {
        return [
            CreateAction::make()
            ->mountUsing(function (Form $form, array $arguments) {
                $form->fill([
                    'start_at' => $arguments['start'] ?? null,
                    'end_at' => $arguments['end'] ?? null,
                    // Menambahkan filter saat pembuatan acara
                    'user_id' => $this->filters['rencana_kinerja_user']['user_id'] ?? null,
                    'tempat' => $this->filters['tempat'] ?? null,
                    'kategori' => $this->filters['kategori'] ?? null,
                ]);
            }),
            EditAction::make()
                ->form(fn() => $this->getFormSchema())
                ->mountUsing(function (Form $form, ?RencanaKinerja $record, array $arguments) {
                    $form->fill([
                        'proyek_id' => $record->proyek_id,
                        'rencana_kinerja' => $record->rencana_kinerja,
                        'description' => $record->description,
                        'start_at' => $arguments['event']['start'] ?? $record->start_at,
                        'end_at' => $arguments['event']['end'] ?? $record->end_at,
                        'tempat' => $record->tempat,
                        'tempat_lainnya' => $record->tempat_lainnya,
                        'users' => $record->users->pluck('id')->toArray(),
                        'kategori' => $record->kategori,
                        'target' => $record->target,
                        'satuan' => $record->satuan,
                        'realisasi' => $record->realisasi,
                        'daftar_hadir' => $record->daftar_hadir,
                        'rekap_daftar_hadir' => $record->rekap_daftar_hadir,
                        'link_materi' => $record->link_materi,
                        'notulensi' => $record->notulensi,
                    ]);
                })
                ->after(function () {

                    // Dispatch event untuk refresh kalender
                    $this->dispatch('filament-fullcalendar--refresh');
                }),
            DeleteAction::make()
        ];
    }
}
