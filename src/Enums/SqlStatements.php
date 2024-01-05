<?php

namespace EyadBereh\LaravelDbQueryLogger\Enums;

enum SqlStatements: string
{
    case SELECT = 'SELECT';
    case INSERT = 'INSERT';
    case UPDATE = 'UPDATE';
    case DELETE = 'DELETE';
}
