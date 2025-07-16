<?php

namespace Lunar\Admin\Support\Pages;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Lunar\Admin\Filament\Resources\ActivityResource;

abstract class BaseActivityLog extends ManageRelatedRecords
{
    use Concerns\ExtendsFooterWidgets;
    use Concerns\ExtendsHeaderActions;
    use Concerns\ExtendsHeaderWidgets;
    use Concerns\ExtendsHeadings;
    use \Lunar\Admin\Support\Concerns\CallsHooks;
    use \Lunar\Admin\Support\Concerns\RelationManagers\ExtendsTables;

    protected static string $relationship = 'activities';

    public function getTitle(): string|Htmlable
    {
        return 'Activity Log';
    }

    public static function getDefaultTable(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('lunarpanel::activity.table.description'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('log_name')
                    ->label(__('lunarpanel::activity.table.log')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('lunarpanel::activity.table.logged_at'))
                    ->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->label(__('lunarpanel::activity.table.event'))
                    ->multiple()
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('logged_from')
                            ->label(__('lunarpanel::activity.table.logged_from')),
                        Forms\Components\DatePicker::make('logged_until')
                            ->label(__('lunarpanel::activity.table.logged_until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['logged_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['logged_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['logged_from'] ?? null) {
                            $indicators['logged_from'] = 'Created from '.Carbon::parse($data['logged_from'])->toFormattedDateString();
                        }

                        if ($data['logged_until'] ?? null) {
                            $indicators['logged_until'] = 'Created until '.Carbon::parse($data['logged_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make('view')
                    ->label('View')
                    ->modal(true)
                    ->form(fn ($form) => ActivityResource::getDefaultForm($form)),
            ])
            ->defaultSort('id', 'DESC');
    }
}
