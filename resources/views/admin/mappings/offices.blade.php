@extends('layouts.app')
@push('style')
    <style>
    </style>
@endpush
@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <div class="card form-card">
                <div class="card-header bg-white form-header">
                    <h1 class="form-title">Data Field Mapping</h1>
                    <p class="form-subtitle mb-0">Configure your data fields by mapping source columns to destination fields
                    </p>
                </div>

                <div class="row g-3 align-items-center table-header px-4 py-2 mx-0">
                    <div class="col-5">Source Field</div>
                    <div class="col-5">Destination Field</div>
                    <div class="col-2 text-center">Action</div>
                </div>

                <div class="card-body p-0">
                    <div class="row g-3 align-items-center table-row px-4 py-3 mx-0 border-bottom">
                        <div class="col-5">
                            <input type="text" class="form-control readonly-input" value="Customer ID" readonly>
                        </div>
                        <div class="col-5">
                            <select class="form-select">
                                <option selected disabled>Select an option...</option>
                                <option value="1">First Name</option>
                                <option value="2">Last Name</option>
                                <option value="3">Email Address</option>
                                <option value="4">Phone Number</option>
                            </select>
                        </div>
                        <div class="col-2 text-center">
                            <button class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row g-3 align-items-center table-row px-4 py-3 mx-0 border-bottom">
                        <div class="col-5">
                            <input type="text" class="form-control readonly-input" value="Full Name" readonly>
                        </div>
                        <div class="col-5">
                            <select class="form-select">
                                <option selected disabled>Select an option...</option>
                                <option value="1">First Name</option>
                                <option value="2">Last Name</option>
                                <option value="3">Email Address</option>
                                <option value="4">Phone Number</option>
                            </select>
                        </div>
                        <div class="col-2 text-center">
                            <button class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row g-3 align-items-center table-row px-4 py-3 mx-0 border-bottom">
                        <div class="col-5">
                            <input type="text" class="form-control readonly-input" value="Contact Email" readonly>
                        </div>
                        <div class="col-5">
                            <select class="form-select">
                                <option selected disabled>Select an option...</option>
                                <option value="1">First Name</option>
                                <option value="2">Last Name</option>
                                <option value="3">Email Address</option>
                                <option value="4">Phone Number</option>
                            </select>
                        </div>
                        <div class="col-2 text-center">
                            <button class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row g-3 align-items-center table-row px-4 py-3 mx-0 border-bottom">
                        <div class="col-5">
                            <input type="text" class="form-control readonly-input" value="Mobile Number" readonly>
                        </div>
                        <div class="col-5">
                            <select class="form-select">
                                <option selected disabled>Select an option...</option>
                                <option value="1">First Name</option>
                                <option value="2">Last Name</option>
                                <option value="3">Email Address</option>
                                <option value="4">Phone Number</option>
                            </select>
                        </div>
                        <div class="col-2 text-center">
                            <button class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>

                    <div class="row g-3 align-items-center table-row px-4 py-3 mx-0 border-bottom">
                        <div class="col-5">
                            <input type="text" class="form-control readonly-input" value="Shipping Address" readonly>
                        </div>
                        <div class="col-5">
                            <select class="form-select">
                                <option selected disabled>Select an option...</option>
                                <option value="1">First Name</option>
                                <option value="2">Last Name</option>
                                <option value="3">Email Address</option>
                                <option value="4">Phone Number</option>
                            </select>
                        </div>
                        <div class="col-2 text-center">
                            <button class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card-footer form-actions d-flex justify-content-end gap-2">
                    <button class="btn btn-outline-secondary">
                        <i class="bi bi-plus me-1"></i> Add New Field
                    </button>
                    <button class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save Mapping
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
@endpush
