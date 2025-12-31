@extends('layouts.app')
@push('style')
    <style>
    </style>
@endpush
@section('content')
    <div class="row">
        <div class="col-12 col-lg-8">
            <div class="card radius-10">
                <div class="card-header border-bottom-0 bg-transparent">
                    <div class="d-lg-flex align-items-center">
                        <div>
                            <h6 class="font-weight-bold mb-2 mb-lg-0">Hospital Activities</h6>
                        </div>
                        <div class="ms-lg-auto mb-2 mb-lg-0">
                            <div class="btn-group-round">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-white">Last 1 Year</button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="javaScript:;">Last Month</a>
                                        <a class="dropdown-item" href="javaScript:;">Last Week</a>
                                    </div>
                                    <button type="button" class="btn btn-white dropdown-toggle dropdown-toggle-split"
                                        data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <span
                                            class="visually-hidden">Toggle
                                            Dropdown</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chart1"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card radius-10 bg-gradient-burning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <img src="assets/images/icons/appointment-book.png" width="45" alt="" />
                        <div class="ms-auto text-end">
                            <p class="mb-0 text-white"><i class='bx bxs-arrow-from-bottom'></i> 2.69%</p>
                            <p class="mb-0 text-white">Since Last Month</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3">
                        <div class="flex-grow-1">
                            <p class="mb-1 text-white">Appointments</p>
                            <h4 class="mb-0 text-white font-weight-bold">1879</h4>
                        </div>
                        <div id="chart2"></div>
                    </div>
                </div>
            </div>
            <div class="card radius-10 bg-gradient-blues">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <img src="assets/images/icons/surgery.png" width="45" alt="" />
                        <div class="ms-auto text-end">
                            <p class="mb-0 text-white"><i class='bx bxs-arrow-from-bottom'></i> 3.56%</p>
                            <p class="mb-0 text-white">Since Last Month</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3">
                        <div class="flex-grow-1">
                            <p class="mb-1 text-white">Surgery</p>
                            <h4 class="mb-0 text-white font-weight-bold">3768</h4>
                        </div>
                        <div id="chart3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
@endpush
