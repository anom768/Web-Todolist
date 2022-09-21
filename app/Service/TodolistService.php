<?php

namespace BangkitAnomSedhayu\Belajar\PHP\MVC\Service;

use BangkitAnomSedhayu\Belajar\PHP\MVC\Model\UserTodolistRequest;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Model\UserTodolistResponse;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Repository\TodolistRepository;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Exception\ValidationException;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Config\Database;
use BangkitAnomSedhayu\Belajar\PHP\MVC\Domain\Todolist;

class TodolistService
{
    private TodolistRepository $todolistRepository;

    public function __construct(TodolistRepository $todolistRepository)
    {
        $this->todolistRepository = $todolistRepository;
    }

    public function add(UserTodolistRequest $request) : UserTodolistResponse
    {
        $this->validateUserTodolistRequest($request);

        try {
            Database::beginTransaction();
            $todolist = new Todolist();
            $todolist->id = $request->id;
            $todolist->id_user = $request->id_user;
            $todolist->todolist = $request->todolist;
            $this->todolistRepository->save($todolist);
            
            $response = new UserTodolistResponse();
            $response->todolist = $todolist;
            Database::commitTransaction();
            return $response;
        } catch (ValidationException $exception) {
            Database::rollbackTransaction();
            throw $exception;
        }
    }

    private function validateUserTodolistRequest(UserTodolistRequest $request)
    {
        if ($request->todolist == null || trim($request->todolist) == "") {
                throw new ValidationException("Todolist can not BLANK");
            }
    }
}