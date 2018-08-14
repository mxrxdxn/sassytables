<div class="table-container">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    @foreach ($table->columns as $column)
                        <th class="{{ $column['class'] ?? '' }}">
                            @if (! empty($column['sortable']) && $column['sortable'] === true)
                                <a href="{{ $table->url->sort($column['column']) }}">
                                    {{ $column['name'] }}

                                    @if ($table->sort === $column['column'])
                                        @if ($table->order === 'asc')
                                            @icon (['icon' => 'fa-sort-up'])
                                            @endicon
                                        @else
                                            @icon (['icon' => 'fa-sort-down'])
                                            @endicon
                                        @endif
                                    @endif
                                </a>
                            @else
                                <a href="#">
                                    {{ $column['name'] }}
                                </a>
                            @endif
                        </th>
                    @endforeach

                    @if ($table->canEdit)
                        <th></th>
                    @endif

                    @if ($table->canDelete)
                        <th></th>
                    @endif
                </tr>
            </thead>

            @if (count($table->object) > 0)
                <tbody>
                    @foreach ($table->object as $obj)
                        <tr>
                            @foreach ($table->columns as $column)
                                <td class="{{ $column['class'] ?? '' }}">
                                    @if (! empty($column['links']))
                                        <a href="{{ optional($obj->url)->view }}">
                                            @if (! empty($column['links'][4]))
                                                {!! optional(optional(optional(optional(optional($obj->{$column['links'][0]})->{$column['links'][1]})->{$column['links'][2]})->{$column['links'][3]})->{$column['links'][4]})->{$column['column']} !!}
                                            @elseif (! empty($column['links'][3]))
                                                {!! optional(optional(optional(optional($obj->{$column['links'][0]})->{$column['links'][1]})->{$column['links'][2]})->{$column['links'][3]})->{$column['column']} !!}
                                            @elseif (! empty($column['links'][2]))
                                                {!! optional(optional(optional($obj->{$column['links'][0]})->{$column['links'][1]})->{$column['links'][2]})->{$column['column']} !!}
                                            @elseif (! empty($column['links'][1]))
                                                {!! optional(optional($obj->{$column['links'][0]})->{$column['links'][1]})->{$column['column']} !!}
                                            @elseif (! empty($column['links'][0]))
                                                {!! optional($obj->{$column['links'][0]})->{$column['column']} !!}
                                            @endif
                                        </a>
                                    @elseif (! empty($column['column']))
                                        <a href="{{ optional($obj->url)->view }}">
                                            @if (! empty($column['boolean']) && $column['boolean'] === true)
                                                @if ($obj->{$column['column']} === true || $obj->{$column['column']} === 1)
                                                    @icon (['icon' => 'fa-check'])
                                                    @endicon
                                                @else
                                                    @icon (['icon' => 'fa-times'])
                                                    @endicon
                                                @endif
                                            @else
                                                {!! $obj->{$column['column']} !!}
                                            @endif
                                        </a>
                                    @endif
                                </td>
                            @endforeach

                            @if ($table->canEdit)
                                <td>
                                    <a href="{{ optional($obj->url)->edit ?? '#' }}" class="table-edit">
                                        @icon (['icon' => 'fa-edit'])
                                        @endicon
                                    </a>
                                </td>
                            @endif

                            @if ($table->canDelete)
                                <td>
                                    <a href="{{ optional($obj->url)->delete ?? '#' }}" class="table-delete" data-delete-prompt>
                                        @icon (['icon' => 'fa-trash-alt'])
                                        @endicon
                                    </a>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            @else
                <tbody>
                    <tr>
                        <td colspan="5">
                            <a>{{ $table->noDataString }}</a>
                        </td>
                    </tr>
                </tbody>
            @endif
        </table>
    </div>
</div>

@if ($table->paginated)
    {{ $table->object->appends($table->appends)->links() }}
@endif