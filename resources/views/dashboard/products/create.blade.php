
<form method="POST" action="{{ route('products.store') }}" class="space-y-6">
    @csrf

    <!-- Name -->
    <div>
        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
        <input id="name" name="name" type="text" required autofocus class="mt-1 block w-full">
        @error('name')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    
    <!-- Purchase Price -->
    <div>
        <label for="purchase_price" class="block text-sm font-medium text-gray-700">Purchase Price</label>
        <input id="purchase_price" name="purchase_price" type="number" step="0.01" required class="mt-1 block w-full">
        @error('purchase_price')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Sale Price -->
    <div>
        <label for="sale_price" class="block text-sm font-medium text-gray-700">Sale Price</label>
        <input id="sale_price" name="sale_price" type="number" step="0.01" required class="mt-1 block w-full">
        @error('sale_price')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Quantity -->
    <div>
        <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
        <input id="quantity" name="quantity" type="number" required class="mt-1 block w-full">
        @error('quantity')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Status -->
    <div>
        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
        <select id="status" name="status" required class="mt-1 block w-full">
            <option value="1">Active</option>
            <option value="0">Inactive</option>
        </select>
        @error('status')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Submit Button -->
    <div>
        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md">
            Save
        </button>
    </div>
</form>

