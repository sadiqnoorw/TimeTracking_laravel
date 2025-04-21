<x-app-layout>
    <div class="container py-4">
        <h2 class="mb-4">Time Tracking</h2>

        <div class="row g-4">
            {{-- Start Work --}}
            <div class="col-md-12"><div class="p-3 mb-2 bg-info text-white">Start Work</div></div>
            <div class="col-md-6">
                <div class="card border-primary shadow-sm">
                    <div class="card-body">
                       
                        <form method="POST" id="startWorkForm" action="{{ route('start.work') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="work_type_id" class="form-label">Work Type</label>
                                <select name="work_type_id" id="work_type_id" class="form-select" required>
                                    @foreach($workTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Start Work</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Stop Work --}}
            <div class="col-md-6">
                <div class="card border-danger shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Stop Work</h5>
                        <form method="POST" id="stopWorkForm" action="{{ route('stop.work') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100">Stop Work</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4 mt-3"><div class="col-md-12"><div class="p-3 mb-2 bg-info text-white">Start Break</div></div></div>
        <div class="row g-4">

            {{-- Start Break --}}
            <div class="col-md-6">
                <div class="card border-warning shadow-sm">
                    <div class="card-body">
                        <form method="POST" id="startBreakForm" action="{{ route('start.break') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="break_reason_id" class="form-label">Break Reason</label>
                                <select name="break_reason_id" id="break_reason_id" class="form-select" required>
                                    @foreach($breakReasons as $reason)
                                        <option value="{{ $reason->id }}">{{ $reason->reason }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-warning w-100">Start Break</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Stop Break --}}
            <div class="col-md-6">
                <div class="card border-success shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Stop Break</h5>
                        <form method="POST" id="stopBreakForm" action="{{ route('stop.break') }}">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">Stop Break</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('time.report') }}" class="btn btn-info btn-lg">View Report</a>
        </div>
    </div>
    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script>
            $(document).ready(function () {
                // Common handler for all forms
                $('form').on('submit', function (e) {
                    e.preventDefault();
                    const form = $(this);
                    const url = form.attr('action');
                    const method = form.attr('method');
                    const data = form.serialize();

                    $.ajax({
                        url: url,
                        method: method,
                        data: data,
                        success: function (response) {

                            switch (response.action) {
                                case 'disable_start_work':
                                    // Disable all, then enable only Stop Work
                                    $('form button[type="submit"]').prop('disabled', true);
                                    $('form[action$="stop-work"]').find('button').prop('disabled', false);
                                    break;

                                case 'disable_start_break':
                                    // Disable all, then enable only Stop Break
                                    $('form button[type="submit"]').prop('disabled', true);
                                    $('form[action$="stop-break"]').find('button').prop('disabled', false);
                                    break;

                                case 'disable_stop_work':
                                case 'disable_stop_break':
                                    // Disable only Stop Work and Stop Break
                                    $('form[action$="stop-work"]').find('button').prop('disabled', true);
                                    $('form[action$="stop-break"]').find('button').prop('disabled', true);
                                    // Enable Start buttons
                                    $('form[action$="start-work"]').find('button').prop('disabled', false);
                                    $('form[action$="start-break"]').find('button').prop('disabled', false);
                                    break;

                                default:
                                    // Fallback: enable everything
                                    $('form button[type="submit"]').prop('disabled', false);
                            }
                        },
                        error: function (xhr) {
                            alert(xhr.responseJSON.message);
                        }
                    });
                });
            });


            document.addEventListener("DOMContentLoaded", function() {
                let status = @json($status);

                // Disable all form buttons initially
                document.querySelectorAll('form button[type=submit]').forEach(btn => btn.disabled = true);

                if (status.activeWork) {
                    // Only enable Stop Work
                    document.querySelector('#stopWorkForm button[type=submit]').disabled = false;
                } else if (status.activeBreak) {
                    // Only enable Stop Break
                    document.querySelector('#stopBreakForm button[type=submit]').disabled = false;
                } else {
                    // No active work or break — enable both start buttons
                    document.querySelector('#startWorkForm button[type=submit]').disabled = false;
                    document.querySelector('#startBreakForm button[type=submit]').disabled = false;

                    // Stop buttons stay disabled (they already are from the initial line)
                }
            });


        </script>
        @endpush

</x-app-layout>



