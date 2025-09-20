<table class="table table-bordered">
    <thead>
    <tr>
        <th>From Date</th>
        <th>To Date</th>
        <th>Target Amount</th>
        <th>Status</th>
        <th>Created At</th>
    </tr>
    </thead>

    <tbody>
    @foreach($targets as $target)
        <tr>
            <td>
                @if($target->from_date)
                    {{ \Carbon\Carbon::parse($target->from_date)->format('j F, Y') }}
                @else
                    N/A
                @endif
            </td>
            <td>
                @if($target->to_date)
                    {{ \Carbon\Carbon::parse($target->to_date)->format('j F, Y') }}
                @else
                    N/A
                @endif
            </td>
            <td>৳{{ number_format($target->amount, 2) }}</td>
            <td>
                @if($target->status == 1)
                    <span class="label label-success">Active</span>
                @else
                    <span class="label label-danger">Inactive</span>
                @endif
            </td>
            <td>{{ $target->created_at->format('j F, Y') }}</td>
        </tr>
    @endforeach
    </tbody>

    <tfoot>
    <tr>
        <th colspan="2">Total Target Amount</th>
        <th>৳{{ number_format($targets->sum('amount'), 2) }}</th>
        <th colspan="2"></th>
    </tr>
    </tfoot>
</table>
