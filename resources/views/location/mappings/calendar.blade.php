@extends('layouts.app')
@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .mappingArea {
            max-height: 500px;
            overflow: scroll;
        }

        #loader {
            margin: 10px;
        }
    </style>
    <style>
        :root {
            --primary-color: #3b82f6;
            --accent-color: #14b8a6;
            --secondary-color: #8b5cf6;
            --success-color: #22c55e;
        }

        .fetch-modal .modal-content {
            border: none;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal-header-gradient {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color), var(--secondary-color));
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .modal-header-gradient::before {
            content: '';
            position: absolute;
            top: -20px;
            left: -20px;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            filter: blur(20px);
        }

        .modal-header-gradient::after {
            content: '';
            position: absolute;
            bottom: -30px;
            right: -30px;
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            filter: blur(30px);
        }

        .modal-header-gradient h5 {
            color: white;
            font-weight: 700;
            margin-bottom: 0.25rem;
            position: relative;
            z-index: 1;
        }

        .modal-header-gradient p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.875rem;
            margin: 0;
            position: relative;
            z-index: 1;
        }

        .fetch-item {
            padding: 1rem;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            animation: slideIn 0.5s ease-out forwards;
            opacity: 0;
            transform: translateY(10px);
        }

        .fetch-item:hover {
            background: #f8fafc;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .fetch-item:nth-child(1) {
            animation-delay: 0ms;
        }

        .fetch-item:nth-child(2) {
            animation-delay: 100ms;
        }

        .fetch-item:nth-child(3) {
            animation-delay: 200ms;
        }

        @keyframes slideIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 0.75rem;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(20, 184, 166, 0.1));
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .fetch-item:hover .icon-box {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(20, 184, 166, 0.2));
        }

        .icon-box i {
            font-size: 1.25rem;
            color: var(--primary-color);
        }

        .progress {
            height: 6px;
            border-radius: 3px;
            background: #e2e8f0;
            overflow: hidden;
        }

        .progress-bar {
            border-radius: 3px;
            transition: width 0.7s ease-out;
        }

        .progress-bar.pending {
            width: 50%;
            background: linear-gradient(90deg, rgba(20, 184, 166, 0.5), var(--accent-color));
            animation: pulse 1.5s infinite;
        }

        .progress-bar.success {
            width: 100%;
            background: linear-gradient(90deg, var(--success-color), rgba(34, 197, 94, 0.7));
        }

        .progress-bar.error {
            width: 100%;
            background: linear-gradient(90deg, #ef4444, rgba(239, 68, 68, 0.7));
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .status-icon {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .status-icon.pending i {
            color: var(--accent-color);
            animation: spin 1s linear infinite;
        }

        .status-icon.pending::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: rgba(20, 184, 166, 0.3);
            animation: ping 1s cubic-bezier(0, 0, 0.2, 1) infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        @keyframes ping {

            75%,
            100% {
                transform: scale(2);
                opacity: 0;
            }
        }

        .status-icon.success {
            background: rgba(34, 197, 94, 0.2);
            border-radius: 50%;
            animation: scaleIn 0.3s ease-out;
        }

        .status-icon.success i {
            color: var(--success-color);
            font-size: 0.875rem;
        }

        .status-icon.error {
            background: rgba(239, 68, 68, 0.2);
            border-radius: 50%;
            animation: scaleIn 0.3s ease-out;
        }

        .status-icon.error i {
            color: #ef4444;
            font-size: 0.875rem;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }

            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .success-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(34, 197, 94, 0.1);
            color: var(--success-color);
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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
                                <th>Service</th>
                                <th>Calendar</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="card-footer form-actions d-flex justify-content-end gap-2">
                    {{-- <button class="btn btn-outline-secondary">
                        <i class="bi bi-plus me-1"></i> Add New Field
                    </button> --}}
                    <button class="btn btn-primary" id="saveMapping">
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


    <div class="modal fade fetch-modal" id="fetchModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header-gradient text-center">
                    <div class="w-100">
                        <h5>Loading Data</h5>
                        <p>Please wait while we fetch your data</p>
                    </div>
                </div>

                <div class="modal-body p-4">

                    <div class="fetch-item d-flex align-items-center gap-3 mb-3">
                        <div class="icon-box">
                            <i class="fa fa-cloud"></i>
                        </div>
                        <div class="flex-grow-1">
                            <span class="fw-medium">Fetch Generic Flags</span>
                            <div class="progress mt-2">
                                <div class="progress-bar pending" id="progress-flags"></div>
                            </div>
                        </div>
                        <div class="status-icon pending" id="status-flags">
                            <i class="fa fa-sync fa-spin"></i>
                        </div>
                    </div>

                    <div class="fetch-item d-flex align-items-center gap-3">
                        <div class="icon-box">
                            <i class="fa fa-wrench"></i>
                        </div>
                        <div class="flex-grow-1">
                            <span class="fw-medium">Fetch Service Types</span>
                            <div class="progress mt-2">
                                <div class="progress-bar pending" id="progress-services"></div>
                            </div>
                        </div>
                        <div class="status-icon pending" id="status-services">
                            <i class="fa fa-sync fa-spin"></i>
                        </div>
                    </div>

                    <div class="fetch-item d-flex align-items-center gap-3 mb-3">
                        <div class="icon-box">
                            <i class="fa fa-users"></i>
                        </div>
                        <div class="flex-grow-1">
                            <span class="fw-medium">Fetch Customers</span>
                            <div class="progress mt-2">
                                <div class="progress-bar pending" id="progress-customers"></div>
                            </div>
                        </div>
                        <div class="status-icon pending" id="status-customers">
                            <i class="fa fa-sync fa-spin"></i>
                        </div>
                    </div>



                    <div class="text-center pt-4 d-none" id="success-message">
                        <span class="success-badge">
                            <i class="fa fa-check"></i>
                            All data loaded successfully!
                        </span>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {

            let calendars = [];
            let servicesData = [];

            $('#loader').show();

            $.ajax({
                url: '{{ route('mappings.calendar.fetch.data') }}',
                type: 'GET',
                dataType: 'json',
                success: function(res) {

                    calendars = res.calendars;
                    servicesData = res.data;

                    let tbody = $('#officesTable tbody');
                    tbody.empty();

                    servicesData.forEach(function(service) {

                        let row = $(`
                    <tr data-service-id="${service.service_id}">
                        <td>${service.service_name ?? '-'}</td>
                        <td>
                            <input type="text"
                                   class="form-control destination-field"
                                   placeholder="Select Calendar"
                                   readonly>
                        </td>
                    </tr>
                `);

                        /* ✅ ONLY DB mapping */
                        if (service.selected_calendar_id) {

                            let calendar = calendars.find(
                                c => c.id == service.selected_calendar_id
                            );

                            row.find('.destination-field')
                                .attr('data-calendar-id', service.selected_calendar_id);

                            if (calendar) {
                                row.find('.destination-field').val(calendar.name);
                            }
                        }

                        tbody.append(row);
                    });

                    $('#loader').hide();
                    $('#officesTable').show();
                }
            });

            /* Calendar Dropdown */
            $(document).on('click', '.destination-field', function() {

                $('.location-dropdown').remove();

                let input = $(this);
                let offset = input.offset();

                let dropdown = $('#locationDropdownTemplate')
                    .clone()
                    .attr('id', '')
                    .addClass('location-dropdown')
                    .show();

                dropdown.css({
                    top: offset.top + input.outerHeight(),
                    left: offset.left,
                    width: input.outerWidth()
                });

                let ul = dropdown.find('.location-list');
                ul.empty();

                calendars.forEach(function(calendar) {
                    ul.append(`
                <li class="list-group-item list-group-item-action location-item"
                    data-id="${calendar.id}">
                    ${calendar.name}
                </li>
            `);
                });

                $('body').append(dropdown);

                dropdown.find('.search-location').on('input', function() {
                    let val = $(this).val().toLowerCase();
                    dropdown.find('.location-item').each(function() {
                        $(this).toggle($(this).text().toLowerCase().includes(val));
                    });
                });

                dropdown.find('.location-item').on('click', function() {

                    input
                        .val($(this).text())
                        .attr('data-calendar-id', $(this).data('id'));

                    dropdown.remove();
                });

                $(document).on('click.locationDropdown', function(e) {
                    if (!$(e.target).closest('.destination-field, .location-dropdown').length) {
                        dropdown.remove();
                        $(document).off('click.locationDropdown');
                    }
                });
            });

            /* Save Mapping */
            $('#saveMapping').on('click', function() {

                let btn = $(this);
                btn.prop('disabled', true).text('Processing...');

                let mappings = [];

                $('#officesTable tbody tr').each(function() {

                    let serviceId = $(this).data('service-id');
                    let calendarId = $(this).find('.destination-field').data('calendar-id');

                    if (serviceId && calendarId) {
                        mappings.push({
                            service_id: serviceId,
                            calendar_id: calendarId
                        });
                    }
                });

                if (!mappings.length) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Mapping Selected',
                        text: 'Please select at least one service-calendar mapping.'
                    });

                    btn.prop('disabled', false)
                        .html('<i class="bi bi-save me-1"></i> Save Mapping');
                    return;
                }

                $.post("{{ route('mappings.calendar.store.data') }}", {
                        _token: "{{ csrf_token() }}",
                        mappings: mappings
                    })
                    .done(function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: res.message
                        });
                    })
                    .fail(function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message ?? 'Something went wrong'
                        });
                    })
                    .always(function() {
                        btn.prop('disabled', false)
                            .html('<i class="bi bi-save me-1"></i> Save Mapping');
                    });
            });

        });
    </script>
@endpush
