<?php
declare(strict_types=1);

namespace Nicmaxcarter\SimpleQueue\Example;

use Phinx\Migration\AbstractMigration;

final class AddQueueTable extends AbstractMigration
{

    /**
     * Up method for migrating up
     * Add queue table to handle more requests
     */
    public function up(): void
    {
        $unsigned = ['signed' => false];
        $foreignOptions = ['delete'=> 'RESTRICT', 'update'=> 'RESTRICT'];

        $valueString = 'complete,progress,waiting,error';
        $enumVals = ['values' => $valueString, 'default' => 'waiting'];

        /*
         * action
         */
        $action = $this->table('action', $unsigned);
        $action->addColumn('name', 'string', ['limit' => 36])
             ->addColumn('description', 'string', ['limit' => 128])
             ->create();

        $action->insert(self::action())->saveData();

        /*
         * task_queue
         */
        $queue = $this->table('task_queue', $unsigned);
        $queue->addColumn('name','string', ['limit' => 36])
             ->addColumn('action','integer', ['signed' => false])
             ->addColumn('company','integer', ['signed' => false])
             ->addColumn('start_time', 'datetime', ['null' => true])
             ->addColumn('end_time', 'datetime', ['null' => true])
             ->addColumn('elapse','integer', ['null' => true])
             ->addColumn('log','string', ['limit' => 128, 'null' => true])
             ->addColumn('message','string', ['limit' => 128, 'null' => true])
             ->addColumn('status', 'enum', $enumVals)
             ->addColumn('immediate','boolean', ['default' => false])
             ->addColumn('arguments','string', ['limit' => 128, 'null' => true])
             ->addForeignKey('action', 'action', 'id', $foreignOptions)
             ->addForeignKey('company', 'company', 'id', $foreignOptions)
             ->addTimestamps()
             ->create();

        $queue->insert(self::queue())->saveData();

    }


    /**
     * Up method for rolling back
     */
    public function down()
    {
        $this->table('task_queue')->drop()->save();
        $this->table('action')->drop()->save();
    }

    /*
     * return rows to insert for action table
     */
    private static function action()
    {
        return [
            [
                'id' => 1,
                'name' => 'fetch',
                'description' => 'fetch settlement'
            ],
            [
                'id' => 2,
                'name' => 'run',
                'description' => 'run flsa'
            ],
            [
                'id' => 3,
                'name' => 'fetch and run',
                'description' => 'fetch settlement and run flsa'
            ],
            [
                'id' => 4,
                'name' => 'sample',
                'description' => 'sample merged task'
            ],
            [
                'id' => 5,
                'name' => 'parse settlement',
                'description' => 'parse an uploaded settlement'
            ],
        ];
    }

    /*
     * return rows to insert for task_queue table
     */
    private static function queue()
    {
        return [
            [
                'name' => 'sample merged task',
                'action' => 4,
                'company' => 1,
                'arguments' => '1,last'
            ],
            [
                'name' => 'Run FLSA',
                'action' => 2,
                'company' => 1,
                'arguments' => '1,last'
            ],
            [
                'name' => 'Run FLSA',
                'action' => 2,
                'company' => 1,
                'arguments' => '1,2022-06-17'
            ],
            [
                'name' => 'Run FLSA',
                'action' => 2,
                'company' => 1,
                'arguments' => '1,2022-06-24'
            ],
            [
                'name' => 'Run FLSA',
                'action' => 2,
                'company' => 2,
                'arguments' => '2,last'
            ],
            [
                'name' => 'Run FLSA',
                'action' => 2,
                'company' => 2,
                'arguments' => '2,2022-06-17'
            ],
            [
                'name' => 'Run FLSA',
                'action' => 2,
                'company' => 2,
                'arguments' => '2,2022-06-24'
            ]
        ];
    }
}
