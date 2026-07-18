@props(['columns' => [], 'data' => [], 'actions' => true])

<div class="sg-table-container">
    <table class="sg-table">
        <thead>
            <tr>
                @foreach($columns as $column)
                    <th class="sg-table-header">
                        {{ $column['label'] ?? $column }}
                    </th>
                @endforeach
                @if($actions)
                    <th class="sg-table-header" style="width:120px;">Actions</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
                <tr class="sg-table-row">
                    @foreach($columns as $key => $column)
                        <td class="sg-table-cell">
                            {{ is_array($column) ? ($column['value'] ?? $item[$key] ?? $item[$column['key'] ?? $key] ?? '—') : ($item[$column] ?? '—') }}
                        </td>
                    @endforeach
                    @if($actions)
                        <td class="sg-table-cell">
                            {{ $actionsSlot ?? '' }}
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
