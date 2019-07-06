<?php
/**
 * FileName: RabbitMQ.php
 * ==============================================
 * Copy right 2016-2017
 * ----------------------------------------------
 * This is not a free software, without any authorization is not allowed to use and spread.
 * ==============================================
 * @author: kong | <iwhero@yeah.com>
 * @date  : 2019-07-05 23:49
 */

namespace swf\queue\job;


use swf\queue\Job;
use swf\queue\connector\RabbitMQ as RabbitMQQueue;
use Interop\Amqp\AmqpConsumer;
use Interop\Amqp\AmqpMessage;
use Exception;

class RabbitMQ extends Job
{
    /**
     * Same as RabbitMQQueue, used for attempt counts.
     */
    public const ATTEMPT_COUNT_HEADERS_KEY = 'attempts_count';

    protected $connection;
    protected $consumer;
    protected $message;

    public function __construct(RabbitMQQueue $connection, AmqpConsumer $consumer, AmqpMessage $message)
    {
        $this->connection = $connection;
        $this->consumer = $consumer;
        $this->message = $message;
        $this->queue = $consumer->getQueue()->getQueueName();
    }

    public function fire()
    {
        $this->resolveAndFire($this->payload());
    }

    public function attempts()
    {
        // set default job attempts to 1 so that jobs can run without retry
        $defaultAttempts = 1;

        return $this->message->getProperty(self::ATTEMPT_COUNT_HEADERS_KEY, $defaultAttempts);
    }

    public function getRawBody()
    {
        return $this->message->getBody();
    }

    public function delete()
    {
        parent::delete();
        $this->consumer->acknowledge($this->message);
    }

    public function release($delay = 0)
    {
        parent::release($delay);

        $this->delete();

        $body = $this->payload();

        /*
         * Some jobs don't have the command set, so fall back to just sending it the job name string
         */
        if (isset($body['data']['command']) === true) {
            $job = $this->unserialize($body);
        } else {
            $job = $this->getName();
        }

        $data = $body['data'];

        $this->connection->release($delay, $job, $data, $this->getQueue(), $this->attempts() + 1);
    }


    /**
     * Get the decoded body of the job.
     *
     * @return array
     */
    public function payload()
    {
        return json_decode($this->getRawBody(), true);
    }

    /**
     * Unserialize job.
     *
     * @param array $body
     *
     * @throws Exception
     *
     * @return mixed
     */
    protected function unserialize(array $body)
    {
        try {
            /* @noinspection UnserializeExploitsInspection */
            return unserialize($body['data']['command']);
        } catch (Exception $exception) {
            if (
                $this->causedByDeadlock($exception) ||
                $this->contains($exception->getMessage(), ['detected deadlock'])
            ) {
                sleep(2);

                return $this->unserialize($body);
            }

            throw $exception;
        }
    }

    /**
     * Determine if the given exception was caused by a deadlock.
     *
     * @param  \Exception  $e
     * @return bool
     */
    protected function causedByDeadlock(Exception $e)
    {
        $message = $e->getMessage();

        return $this->contains($message, [
            'Deadlock found when trying to get lock',
            'deadlock detected',
            'The database file is locked',
            'database is locked',
            'database table is locked',
            'A table in the database is locked',
            'has been chosen as the deadlock victim',
            'Lock wait timeout exceeded; try restarting transaction',
            'WSREP detected deadlock/conflict and aborted the transaction. Try restarting the transaction',
        ]);
    }

    private function contains($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }
}