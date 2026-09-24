<?php

$main_permission_arr = [

    'dashboard' => [
        'route' => 'dashboard',
        'icon' => 'ri-dashboard-2-line',
        'label' => 'Dashboard',
    ],

    'employees' => [
        'route' => 'employees.list',
        'icon' => 'ri-contacts-book-3-line',
        'label' => 'Employees',
    ],

    'admin_users' => [
        'route' => 'admin_user_module.list',
        'icon' => 'ri-shield-user-line',
        'label' => 'Admin User',
    ],

    'team_members' => [
        'route' => 'team_members.list',
        'icon' => 'ri-team-line',
        'label' => 'Team Members',
    ],

    'projects' => [
        'route' => 'projects.list',
        'icon' => 'ri-briefcase-2-line',
        'label' => 'Projects',
    ],

    'all_tasks' => [
        'route' => 'all_tasks',
        'icon' => 'ri-list-check-3',
        'label' => 'All Tasks',
    ],

    'all_my_tasks' => [
        'route' => 'all_my_tasks',
        'icon' => 'ri-user-follow-line',
        'label' => 'Assigned To Me',
    ],

    'all_assigned_tasks' => [
        'route' => 'all_assigned_tasks',
        'icon' => 'ri-user-shared-line',
        'label' => 'Assigned By Me',
    ],

    'team_tasks' => [
        'route' => 'all_team_tasks',
        'icon' => 'ri-todo-line',
        'label' => 'Team Tasks',
    ],

    'my_tasks' => [
        'route' => 'my_tasks',
        'icon' => 'ri-check-double-line',
        'label' => 'My Tasks',
    ],
];


$permission_arr = [

    'admin' => [
        'dashboard' => [
            ...$main_permission_arr['dashboard'],
            'permissions' => [
                'add' => true,
                'edit' => true,
                'view' => true,
                'delete' => true,
            ],
        ],

        'employees' => [
            ...$main_permission_arr['employees'],
            'permissions' => [
                'add' => true,
                'edit' => true,
                'view' => true,
                'delete' => true,
            ],
        ],

        'admin_users' => [
            ...$main_permission_arr['admin_users'],
            'permissions' => [
                'add' => true,
                'edit' => true,
                'view' => true,
                'delete' => true,
            ],
        ],

        'team_members' => [
            ...$main_permission_arr['team_members'],
            'permissions' => [
                'add' => true,
                'edit' => true,
                'view' => true,
                'delete' => true,
            ],
        ],

        'projects' => [
            ...$main_permission_arr['projects'],
            'permissions' => [
                'add' => true,
                'edit' => true,
                'view' => true,
                'delete' => true,
            ],
        ],

        'all_tasks' => [
            ...$main_permission_arr['all_tasks'],
            'permissions' => [
                'add' => false,
                'edit' => true,
                'view' => true,
                'delete' => true,
            ],
        ],

        'all_assigned_tasks' => [
            ...$main_permission_arr['all_assigned_tasks'],
            'permissions' => [
                'add' => false,
                'edit' => true,
                'view' => true,
                'delete' => false,
            ],
        ],
    ],

    'manager' => [
        'dashboard' => [
            ...$main_permission_arr['dashboard'],
            'permissions' => [
                'add' => true,
                'edit' => true,
                'view' => true,
                'delete' => true,
            ],
        ],

        'team_members' => [
            ...$main_permission_arr['team_members'],
            'permissions' => [
                'add' => false,
                'edit' => true,
                'view' => true,
                'delete' => false,
            ],
        ],

        'projects' => [
            ...$main_permission_arr['projects'],
            'permissions' => [
                'add' => true,
                'edit' => true,
                'view' => true,
                'delete' => false,
            ],
        ],

        'all_tasks' => [
            ...$main_permission_arr['all_tasks'],
            'permissions' => [
                'add' => true,
                'edit' => true,
                'view' => true,
                'delete' => false,
            ],
        ],

        'all_my_tasks' => [
            ...$main_permission_arr['all_my_tasks'],
            'permissions' => [
                'add' => false,
                'edit' => true,
                'view' => true,
                'delete' => false,
            ],
        ],

        'all_assigned_tasks' => [
            ...$main_permission_arr['all_assigned_tasks'],
            'permissions' => [
                'add' => false,
                'edit' => true,
                'view' => true,
                'delete' => false,
            ],
        ],

        'team_tasks' => [
            ...$main_permission_arr['team_tasks'],
            'permissions' => [
                'add' => false,
                'edit' => true,
                'view' => true,
                'delete' => false,
            ],
        ],

        'my_tasks' => [
            ...$main_permission_arr['my_tasks'],
            'permissions' => [
                'add' => false,
                'edit' => true,
                'view' => true,
                'delete' => false,
            ],
        ],
    ],


    'employee' => [

        'dashboard' => [
            ...$main_permission_arr['dashboard'],
            'permissions' => [
                'add' => false,
                'edit' => false,
                'view' => true,
                'delete' => false,
            ],
        ],

        'projects' => [
            ...$main_permission_arr['projects'],
            'permissions' => [
                'add' => false,
                'edit' => false,
                'view' => true,
                'delete' => false,
            ],
        ],

        'all_my_tasks' => [
            ...$main_permission_arr['all_my_tasks'],
            'permissions' => [
                'add' => false,
                'edit' => true,
                'view' => true,
                'delete' => false,
            ],
        ],

        'my_tasks' => [
            ...$main_permission_arr['my_tasks'],
            'permissions' => [
                'add' => false,
                'edit' => true,
                'view' => true,
                'delete' => false,
            ],
        ],
    ],
];


return $permission_arr;
