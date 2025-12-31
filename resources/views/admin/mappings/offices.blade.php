@extends('layouts.app')
@push('style')
    <style>
        .mappingArea {
            max-height: 500px;
            overflow: scroll;
        }
    </style>
@endpush
@section('content')
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card form-card">
                <div class="card-header bg-white form-header">
                    <h1 class="form-title">Data Field Mapping</h1>
                    <p class="form-subtitle mb-0">Configure your data fields by mapping source columns to destination fields
                    </p>
                </div>

                <div class="card-body p-0 mappingArea">
                    <div id="loader" style="display:flex; justify-content:center; align-items:center;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>

                    <table class="table table-bordered" id="officesTable" style="display:none;">
                        <thead class="table-light">
                            <tr>
                                <th>Office Name</th>
                                <th>Destination Location</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="card-footer form-actions d-flex justify-content-end gap-2">
                    {{-- <button class="btn btn-outline-secondary">
                        <i class="bi bi-plus me-1"></i> Add New Field
                    </button> --}}
                    <button class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save Mapping
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="locationDropdownTemplate"
        style="display:none; position:absolute; z-index:1000; background:white; border:1px solid #ccc; max-height:200px; overflow-y:auto;">
        <input type="text" class="form-control mb-1 search-location" placeholder="Search...">
        <ul class="list-group location-list mb-0"></ul>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {

            let locations = [];
            let officesData = [];

            $('#loader').show();

            $.ajax({
                url: '{{ route('admin.mappings.offices.fetch.data') }}',
                type: 'GET',
                dataType: 'json',
                success: function(res) {
                    locations = res.locations;
                    officesData = res.data;

                    let tbody = $('#officesTable tbody');
                    officesData.forEach(function(office) {
                        let row = $(`
                    <tr data-office-id="${office.office_id}" data-office-email="${office.office_email}">
                        <td>${office.office_name}</td>
                        <td>
                            <input type="text" class="form-control destination-field" placeholder="Select location" readonly>
                        </td>
                        <td class="text-center action-cell"></td>
                    </tr>
                `);

                        if (office.selected_location_id) {
                            row.find('.action-cell').html(
                                '<button class="btn btn-success btn-sm">Mapped</button>');
                            let loc = locations.find(l => l.id == office.selected_location_id);
                            if (loc) row.find('.destination-field').val(loc.name);
                        } else {
                            let matchLoc = locations.find(l => l.email && l.email == office
                                .office_email);
                            if (matchLoc) {
                                row.find('.destination-field').val(matchLoc.name);
                                row.find('.action-cell').html(
                                    '<button class="btn btn-success btn-sm">Mapped</button>'
                                    );
                            }
                        }

                        tbody.append(row);
                    });

                    $('#loader').hide();
                    $('#officesTable').show();
                }
            });

            $(document).on('click', '.destination-field', function(e) {
                $('.location-dropdown').remove();

                let input = $(this);
                let offset = input.offset();
                let dropdown = $('#locationDropdownTemplate').clone().attr('id', '').addClass(
                    'location-dropdown').show();
                dropdown.css({
                    top: offset.top + input.outerHeight(),
                    left: offset.left,
                    width: input.outerWidth()
                });

                let ul = dropdown.find('.location-list');
                locations.forEach(function(loc) {
                    ul.append(
                        `<li class="list-group-item list-group-item-action location-item" data-id="${loc.id}">${loc.name}</li>`
                        );
                });

                $('body').append(dropdown);

                dropdown.find('.search-location').on('input', function() {
                    let val = $(this).val().toLowerCase();
                    dropdown.find('.location-item').each(function() {
                        $(this).toggle($(this).text().toLowerCase().includes(val));
                    });
                });

                dropdown.find('.location-item').on('click', function() {
                    let locName = $(this).text();
                    let locId = $(this).data('id');

                    input.val(locName);
                    let row = input.closest('tr');
                    row.find('.action-cell').html(
                        '<button class="btn btn-success btn-sm">Mapped</button>');

                    dropdown.remove();
                });

                $(document).on('click.locationDropdown', function(evt) {
                    if (!$(evt.target).closest('.destination-field, .location-dropdown').length) {
                        dropdown.remove();
                        $(document).off('click.locationDropdown');
                    }
                });
            });

        });
    </script>
@endpush
