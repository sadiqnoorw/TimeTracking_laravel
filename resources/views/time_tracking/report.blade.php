<x-app-layout>
    <div class="container my-4">
        <h2 class="mb-4">🗓️ Daily Time Tracking Report</h2>
        @php
            function formatTime($minutes) {
                $seconds = $minutes * 60;
                $hours = floor($seconds / 3600);
                $minutes = floor(($seconds % 3600) / 60);
                $remainingSeconds = $seconds % 60;
                return sprintf('%02d:%02d:%02d', $hours, $minutes, $remainingSeconds);
            }
        @endphp
    
        @forelse($dailyReport as $date => $report)
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <strong>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</strong>
                    <div>
                        <span class="badge bg-success">Work: 
                            {{ formatTime($report['work_time']) }}
                        </span>
                        <span class="badge bg-warning text-dark">Break: 
                            {{ formatTime($report['break_time']) }}
                        </span>
                    </div>
                </div>
    
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>Duration</th>
                                    <th>Work Type</th>
                                    <th>Break Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($report['entries'] as $entry)
                                    <tr>
                                        <td><span class="badge {{ $entry->type == 'work' ? 'bg-success' : 'bg-warning text-dark' }}">{{ ucfirst($entry->type) }}</span></td>
                                        <td>{{ $entry->start_time->format('H:i') }}</td>
                                        <td>{{ $entry->end_time ? $entry->end_time->format('H:i') : '-' }}</td>
                                        <td>
                                            @if($entry->end_time)
                                            {{ $entry->start_time->diff($entry->end_time)->format('%H:%I:%S') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $entry->workType->name ?? '-' }}</td>
                                        <td>{{ $entry->breakReason->reason ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No entries for this day.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info">No time tracking records available.</div>
        @endforelse
    
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary mt-3">← Back to Dashboard</a>
    </div>
    </x-app-layout>
    