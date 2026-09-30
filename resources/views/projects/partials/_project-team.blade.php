{{--    @if(!empty($all_team_members))--}}
<div class="row">
    @if(!empty($project_team) && count($project_team)>0)
        @foreach($project_team as $member_val)
            @php
                $emp_photo = asset('images/users/dummy-avatar.jpg');
                if(!empty($member_val->emp_photo)) {
                    $emp_photo = asset('storage/employees/'. $member_val->emp_photo);
                }
            @endphp
            <div class="col-xl-6 col-lg-6">
                @if(permission_can('team_members', 'view'))
                    <a href="{{route('team_member.view', array_merge(['called_from' => 'project_team', 'member_id' => my_encrypt($member_val->team_member_emp_id), 'pro_id' => my_encrypt($pro_id)],request()->query()))}}"
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
        {!! generate_pagination($project_team,"project_team") !!}
    @else
        {!! generate_no_record_html() !!}
    @endif
</div>
