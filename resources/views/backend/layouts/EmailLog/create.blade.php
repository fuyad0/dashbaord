@extends('backend.app')

@section('title', 'Mail Send')

@section('content')
<style>
    input[type='checkbox']{
        width: 17px;
        height: 17px;
        cursor: pointer;
    }
</style>
    {{-- PAGE-HEADER --}}
    <div class="page-header">
        <div>
            <h1 class="page-title">Mail Send Form</h1>
        </div>
        <div class="ms-auto pageheader-btn">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0);">Mail</a></li>
                <li class="breadcrumb-item active" aria-current="page">Send Mail</li>
            </ol>
        </div>
    </div>
    {{-- PAGE-HEADER END --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="row">
        <div class="col-lg-9">
            <div class="card box-shadow-0">
                <div class="card-body">
                    <form method="post" action="{{ route('send.bulk.mail') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col">
                                 <div class="form-group mb-3">
                                    <label><strong>Select Users</strong></label><br>
                                    <input type="checkbox" id="select-all-user"> Select All

                                    <input type="text" id="user-filter" class="form-control mt-2 w-75" placeholder="Search by name or email">
                                    <hr>
                                    @foreach ($users as $user)
                                        <div class="user-item">
                                            <input type="checkbox" name="user_ids[]" value="{{ $user->id }}">
                                            {{ $user->first_name }} ({{ $user->email }})
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                             <div class="col">
                                 <div class="form-group mb-3">
                                    <label><strong>Select Restaurants</strong></label><br>
                                    <input type="checkbox" id="select-all-restaurant"> Select All
                                    <input type="text" id="restaurant-filter" class="form-control mt-2 w-75" placeholder="Search by name or email">
                                    <hr>
                                    @foreach ($restaurants as $restaurant)
                                        <div class="restaurant-item">
                                            <input type="checkbox" name="restaurant_ids[]" value="{{ $restaurant->id }}">
                                            {{ $restaurant->name }} ({{ $restaurant->email }})
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                             <div class="col">
                                 <div class="form-group mb-3">
                                    <label><strong>Select Coffee</strong></label><br>
                                    <input type="checkbox" id="select-all-coffee"> Select All
                                    <input type="text" id="coffee-filter" class="form-control mt-2 w-75" placeholder="Search by name or email">
                                    <hr>
                                    @foreach ($coffees as $coffee)
                                        <div class="coffee-item">
                                            <input type="checkbox" name="coffee_ids[]" value="{{ $coffee->id }}">
                                            {{ $coffee->name }} ({{ $coffee->email }})
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                             <div class="col">
                                 <div class="form-group mb-3">
                                    <label><strong>Select Deals</strong></label><br>
                                    <input type="checkbox" id="select-all-deal"> Select All
                                    <input type="text" id="deal-filter" class="form-control mt-2 w-75" placeholder="Search by name or email">
                                    <hr>
                                    @foreach ($deals as $deal)
                                        <div class="deal-item">
                                            <input type="checkbox" name="deal_ids[]" value="{{ $deal->id }}">
                                            {{ $deal->name }} ({{ $deal->email }})
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card box-shadow-0">
                <div class="card-body">
                        <div class="form-group mb-3">
                            <label><strong>Select Template</strong></label>
                            <select name="template_id" class="form-control" required>
                                <option value="">-- Choose Template --</option>
                                @foreach ($templates as $template)
                                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label><strong>Schedule Time (optional)</strong></label>
                            <input type="datetime-local" name="schedule_time" class="form-control">
                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary" type="submit">Submit</button>
                            <a href="{{ route('email.index') }}" class="btn btn-danger me-2">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>
document.getElementById('select-all-user').addEventListener('click', function() {
    document.querySelectorAll('input[name="user_ids[]"]').forEach(cb => cb.checked = this.checked);
});

document.getElementById('user-filter').addEventListener('keyup', function () {
    const value = this.value.toLowerCase();
    const items = document.querySelectorAll('.user-item');

    items.forEach(function (item) {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(value) ? '' : 'none';
    });
});


document.getElementById('select-all-restaurant').addEventListener('click', function() {
    document.querySelectorAll('input[name="restaurant_ids[]"]').forEach(cb => cb.checked = this.checked);
});

document.getElementById('restaurant-filter').addEventListener('keyup', function () {
    const value = this.value.toLowerCase();
    const items = document.querySelectorAll('.restaurant-item');

    items.forEach(function (item) {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(value) ? '' : 'none';
    });
});

document.getElementById('select-all-coffee').addEventListener('click', function() {
    document.querySelectorAll('input[name="coffee_ids[]"]').forEach(cb => cb.checked = this.checked);
});

document.getElementById('coffee-filter').addEventListener('keyup', function () {
    const value = this.value.toLowerCase();
    const items = document.querySelectorAll('.coffee-item');

    items.forEach(function (item) {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(value) ? '' : 'none';
    });
});

document.getElementById('select-all-deal').addEventListener('click', function() {
    document.querySelectorAll('input[name="deal_ids[]"]').forEach(cb => cb.checked = this.checked);
});

document.getElementById('deal-filter').addEventListener('keyup', function () {
    const value = this.value.toLowerCase();
    const items = document.querySelectorAll('.deal-item');

    items.forEach(function (item) {
        const text = item.textContent.toLowerCase();
        item.style.display = text.includes(value) ? '' : 'none';
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.body.classList.add('sidenav-toggled');
});
</script>

@endpush