{{--    @if(!empty($all_team_members))--}}
<div class="row">
    @if(!empty($project_manager))
        @php
            $emp_photo = asset('images/users/dummy-avatar.jpg');
            if(!empty($project_manager['emp_photo'])) {
                $emp_photo = asset('storage/employees/'. $project_manager['emp_photo']);
            }
        @endphp
        <div class="col-xl-6 col-lg-6">
            @if(permission_can('team_members', 'view'))
                <a href="{{route('team_member.view', ['called_from' => 'project_team', 'member_id' => my_encrypt($project_data['pro_manager']), 'pro_id' => my_encrypt($pro_id)])}}"
                   target="_self">
                    @endif
                    <div class="card team-members-card">
                        <div class="card-body">
                        <span class="team-member-role-pill">
                            <span class="badge rounded-pill badge-outline-purple me-1 fs-6 badge-soft-purple">Project Manager</span>
                            {{--{!! \App\Enums\UserRoleEnum::tryFrom('manager')->badge() !!}--}}
                        </span>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <img src="{{$emp_photo}}"
                                     class="avatar-lg rounded-3 border border-light border-3 object-fit-cover">
                                <div>
                                    <p class="text-dark fw-medium fs-16 mb-0">{{$project_manager['emp_full_name']}}</p>
                                    <p class="mb-0 badge designation-badge rounded-pill me-1 fs-6">{{$project_manager['emp_designation']->label()}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(permission_can('team_members', 'view'))
                </a>
            @endif
        </div>
    @endif
    @foreach($project_team as $member_val)
        @php
            $emp_photo = asset('images/users/dummy-avatar.jpg');
            if(!empty($member_val->emp_photo)) {
                $emp_photo = asset('storage/employees/'. $member_val->emp_photo);
            }
        @endphp
        <div class="col-xl-6 col-lg-6">
            @if(permission_can('team_members', 'view'))
                <a href="{{route('team_member.view', ['called_from' => 'project_team', 'member_id' => my_encrypt($member_val->team_member_emp_id), 'pro_id' => my_encrypt($pro_id)])}}"
                   target="_self">
                    @endif
                    <div class="card team-members-card">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <img src="{{$emp_photo}}"
                                     class="avatar-lg rounded-3 border border-light border-3">
                                <div>
                                    <p class="text-dark fw-medium fs-16 mb-0">{{$member_val->emp_full_name}}</p>
                                    <p class="mb-0 badge designation-badge rounded-pill me-1 fs-6">{{ucfirst($member_val->emp_designation)}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(permission_can('team_members', 'view'))
                </a>
            @endif
        </div>
    @endforeach
</div>
<div>
    Showing
    {{ $project_team->firstItem() }}
    to
    {{ $project_team->lastItem() + 1 }}
    of
    {{ $project_team->total() + 1 }}
    tasks
</div>

<div>
    {{ $project_team->links() }}
</div>
{{--@else
    {!! generate_no_record_html() !!}
@endif--}}
