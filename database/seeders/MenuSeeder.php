<?php

namespace Database\Seeders;

use App\Models\Menu; // Correct import
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->datas() as $key => $value) {
            $this->createMenu($value);
        }
    }

    private function createMenu($data, $parent_id = null)
    {
        $menu = new Menu([
            'name' => $data['name'],
            'icon' => $data['icon'],
            'route' => $data['route'],
            'description' => $data['description'],
            'sorting' => $data['sorting'],
            'parent_id' => $parent_id,
            'permission_name' => $data['permission_name'],
            'status' => $data['status'],
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ]);

        $menu->save();

        if (isset($data['children']) && is_array($data['children'])) {
            foreach ($data['children'] as $child) {
                $this->createMenu($child, $menu->id);
            }
        }
    }

    private function datas()
    {
        return [
            [
                'name' => 'Dashboard',
                'icon' => 'home',
                'route' => 'backend.dashboard',
                'description' => null,
                'sorting' => 1,
                'permission_name' => 'dashboard',
                'status' => 'Active',
            ],
            [
                'name' => 'Module Make',
                'icon' => 'slack',
                'route' => 'backend.moduleMaker',
                'description' => null,
                'sorting' => 1,
                'permission_name' => 'module maker',
                'status' => 'Active',
            ],
            [
                'name' => 'User Manage',
                'icon' => 'list',
                'route' => null,
                'description' => null,
                'sorting' => 1,
                'permission_name' => 'user-management',
                'status' => 'Active',
                'children' => [
                    [
                        'name' => 'User Add',
                        'icon' => 'plus-circle',
                        'route' => 'backend.admin.create',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'Admin-add',
                        'status' => 'Active',
                    ],
                    [
                        'name' => 'User List',
                        'icon' => 'list',
                        'route' => 'backend.admin.index',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'Admin-list',
                        'status' => 'Active',
                    ],
                ],
            ],
            [
                'name' => 'Permission Manage',
                'icon' => 'unlock',
                'route' => null,
                'description' => null,
                'sorting' => 1,
                'permission_name' => 'permission-management',
                'status' => 'Active',
                'children' => [
                    [
                        'name' => 'Permission Add',
                        'icon' => 'plus-circle',
                        'route' => 'backend.permission.create',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'permission-add',
                        'status' => 'Active',
                    ],
                    [
                        'name' => 'Permission List',
                        'icon' => 'list',
                        'route' => 'backend.permission.index',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'permission-list',
                        'status' => 'Active',
                    ],
                ],
            ],
            [
                'name' => 'Role Manage',
                'icon' => 'layers',
                'route' => null,
                'description' => null,
                'sorting' => 1,
                'permission_name' => 'role-management',
                'status' => 'Active',
                'children' => [
                    [
                        'name' => 'Role Add',
                        'icon' => 'plus-circle',
                        'route' => 'backend.role.create',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'role-add',
                        'status' => 'Active',
                    ],
                    [
                        'name' => 'Role List',
                        'icon' => 'list',
                        'route' => 'backend.role.index',
                        'description' => null,
                        'sorting' => 1,
                        'permission_name' => 'role-list',
                        'status' => 'Active',
                    ],
                ],
            ],



    [
        "name" => "Contact Manage",
        "icon" => "phone-forwarded",
        "route" => null,
        "description" => null,
        "sorting" => 1,
        "permission_name" => "Contact-management",
        "status" => "Active",
        "children" => [
            [
                "name" => "Contact Add",
                "icon" => "plus-circle",
                "route" => "backend.contact.create",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-add",
                "status" => "Active",
            ],
            [
                "name" => "Contact List",
                "icon" => "list",
                "route" => "backend.contact.index",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-list",
                "status" => "Active",
            ],
        ],
    ],


    [
        "name" => "Parsonal Information Manage",
        "icon" => "users",
        "route" => null,
        "description" => null,
        "sorting" => 1,
        "permission_name" => "Parsonal_Info-management",
        "status" => "Active",
        "children" => [
            [
                "name" => "Parsonal Information Add",
                "icon" => "plus-circle",
                "route" => "backend.parsonal_info.create",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-add",
                "status" => "Active",
            ],
            [
                "name" => "Parsonal Information List",
                "icon" => "list",
                "route" => "backend.parsonal_info.index",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-list",
                "status" => "Active",
            ],
        ],
    ],


    [
        "name" => "Skills Manage",
        "icon" => "user-check",
        "route" => null,
        "description" => null,
        "sorting" => 1,
        "permission_name" => "Skills-management",
        "status" => "Active",
        "children" => [
            [
                "name" => "Skills Add",
                "icon" => "plus-circle",
                "route" => "backend.skills.create",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-add",
                "status" => "Active",
            ],
            [
                "name" => "Skills List",
                "icon" => "list",
                "route" => "backend.skills.index",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-list",
                "status" => "Active",
            ],
        ],
    ],


    [
        "name" => "About Manage",
        "icon" => "alert-octagon",
        "route" => null,
        "description" => null,
        "sorting" => 1,
        "permission_name" => "About-management",
        "status" => "Active",
        "children" => [
            [
                "name" => "About Add",
                "icon" => "plus-circle",
                "route" => "backend.about.create",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-add",
                "status" => "Active",
            ],
            [
                "name" => "About List",
                "icon" => "list",
                "route" => "backend.about.index",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-list",
                "status" => "Active",
            ],
        ],
    ],


    [
        "name" => "Work Experience Manage",
        "icon" => "lock",
        "route" => null,
        "description" => null,
        "sorting" => 1,
        "permission_name" => "WorkExperience-management",
        "status" => "Active",
        "children" => [
            [
                "name" => "Work Experience Add",
                "icon" => "plus-circle",
                "route" => "backend.workexperience.create",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-add",
                "status" => "Active",
            ],
            [
                "name" => "Work Experience List",
                "icon" => "list",
                "route" => "backend.workexperience.index",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-list",
                "status" => "Active",
            ],
        ],
    ],


    [
        "name" => "Education Manage",
        "icon" => "layers",
        "route" => null,
        "description" => null,
        "sorting" => 1,
        "permission_name" => "Education-management",
        "status" => "Active",
        "children" => [
            [
                "name" => "Education Add",
                "icon" => "plus-circle",
                "route" => "backend.education.create",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-add",
                "status" => "Active",
            ],
            [
                "name" => "Education List",
                "icon" => "list",
                "route" => "backend.education.index",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-list",
                "status" => "Active",
            ],
        ],
    ],


    [
        "name" => "Featured Project Manage",
        "icon" => "aperture",
        "route" => null,
        "description" => null,
        "sorting" => 1,
        "permission_name" => "FeaturedProject-management",
        "status" => "Active",
        "children" => [
            [
                "name" => "Featured Project Add",
                "icon" => "plus-circle",
                "route" => "backend.featuredproject.create",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-add",
                "status" => "Active",
            ],
            [
                "name" => "Featured Project List",
                "icon" => "list",
                "route" => "backend.featuredproject.index",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-list",
                "status" => "Active",
            ],
        ],
    ],

    
    [
        "name" => "Blog Manage",
        "icon" => "aperture",
        "route" => null,
        "description" => null,
        "sorting" => 1,
        "permission_name" => "Blog-management",
        "status" => "Active",
        "children" => [
            [
                "name" => "Blog Add",
                "icon" => "plus-circle",
                "route" => "backend.blog.create",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-add",
                "status" => "Active",
            ],
            [
                "name" => "Blog List",
                "icon" => "list",
                "route" => "backend.blog.index",
                "description" => null,
                "sorting" => 1,
                "permission_name" => "role-list",
                "status" => "Active",
            ],
        ],
    ],

    //don't remove this comment from menu seeder
        ];
    }
}