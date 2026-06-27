<?php

namespace App\Support;

class PermissionCatalog
{
    /**
     * Canonical list of permission keys grouped by module.
     * key => label
     */
    public static function modules(): array
    {
        return [
            'task' => [
                'label' => 'Task',
                'permissions' => [
                    'task.view'           => 'Xem task',
                    'task.create'         => 'Tạo task',
                    'task.edit'           => 'Sửa task',
                    'task.delete'         => 'Xóa task',
                    'task.change_status'  => 'Đổi trạng thái task',
                ],
            ],
            'subtask' => [
                'label' => 'Sub-task',
                'permissions' => [
                    'subtask.create'      => 'Tạo subtask',
                    'subtask.edit'        => 'Sửa subtask (title, weight, due date)',
                    'subtask.delete'      => 'Xóa subtask',
                    'subtask.toggle'      => 'Tick / bỏ tick hoàn thành',
                    'subtask.cancel'      => 'Đánh dấu không hoàn thành (cancel)',
                ],
            ],
            'comment' => [
                'label' => 'Comment',
                'permissions' => [
                    'comment.view'        => 'Xem comment',
                    'comment.create'      => 'Viết comment (kèm @mention)',
                    'comment.delete'      => 'Xóa comment',
                ],
            ],
            'file' => [
                'label' => 'File',
                'permissions' => [
                    'file.view'           => 'Xem file inline',
                    'file.upload'         => 'Upload file (vào task hoặc comment)',
                    'file.download'       => 'Tải file xuống',
                    'file.delete'         => 'Xóa file',
                ],
            ],
            'member' => [
                'label' => 'Nhân sự',
                'permissions' => [
                    'member.view'         => 'Xem danh sách nhân sự',
                    'member.create'       => 'Thêm nhân sự',
                    'member.edit'         => 'Sửa nhân sự',
                    'member.delete'       => 'Xóa nhân sự',
                ],
            ],
            'department' => [
                'label' => 'Phòng ban',
                'permissions' => [
                    'department.view'     => 'Xem phòng ban',
                    'department.create'   => 'Tạo phòng ban',
                    'department.edit'     => 'Sửa phòng ban',
                    'department.delete'   => 'Xóa phòng ban',
                ],
            ],
            'branch' => [
                'label' => 'Cơ sở',
                'permissions' => [
                    'branch.view'         => 'Xem cơ sở',
                    'branch.create'       => 'Tạo cơ sở',
                    'branch.edit'         => 'Sửa cơ sở',
                    'branch.delete'       => 'Xóa cơ sở',
                ],
            ],
            'role' => [
                'label' => 'Vai trò / Quyền',
                'permissions' => [
                    'role.view'           => 'Xem vai trò',
                    'role.manage'         => 'Tạo / sửa / xóa vai trò + gán quyền',
                ],
            ],
        ];
    }

    public static function allKeys(): array
    {
        $keys = [];
        foreach (self::modules() as $module => $info) {
            foreach (array_keys($info['permissions']) as $k) {
                $keys[] = $k;
            }
        }
        return $keys;
    }

    public static function flat(): array
    {
        $rows = [];
        foreach (self::modules() as $module => $info) {
            foreach ($info['permissions'] as $key => $label) {
                $rows[] = ['key' => $key, 'module' => $module, 'label' => $label];
            }
        }
        return $rows;
    }
}
