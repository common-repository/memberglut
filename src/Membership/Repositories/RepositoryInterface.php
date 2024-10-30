<?php

namespace MemberGlut\Core\Membership\Repositories;

use MemberGlut\Core\Membership\Models\ModelInterface;

interface RepositoryInterface
{
    public function add(ModelInterface $data);

    public function update(ModelInterface $data);

    public function delete($id);

    public function retrieve($id);

    public function updateColumn($id, $column, $value);

    public function retrieveColumn($id, $column);

    public function record_count();
}