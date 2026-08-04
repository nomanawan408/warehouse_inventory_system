@if(count($customers) > 0)
    @foreach ($customers as $customer)
        <tr>
            <td>{{ $customer->name }}</td>
            <td>{{ $customer->business_name }}</td>
            <td>{{ $customer->phone_no }}</td>
            <td>{{ $customer->cnic }}</td>
            <td>{{ number_format($customer->discount, 2) }}%</td>
            <td>
                <div class="d-flex">
                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning btn-sm me-2">Edit</a>
                    <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="6" class="text-center">No customers found.</td>
    </tr>
@endif
