<?php

/**
 * $Id$
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://phing.info>.
 * 
 * @package phing.tasks.ext
 */

require_once 'DocBlox/Parallel/Manager.php';
require_once 'DocBlox/Parallel/Worker.php';
require_once 'DocBlox/Parallel/WorkerPipe.php';

/**
 * Uses the DocBlox_Parallel library to run nested Phing tasks concurrently.
 * 
 * WARNING: this task is highly experimental!
 *
 * @author Michiel Rook <mrook@php.net>
 * @version $Id$
 * @package phing.tasks.ext
 * @see https://github.com/docblox/Parallel
 * @since 2.4.10
 */
class ParallelTask extends SequentialTask
{
    /**
     * The concurrency manager
     * @var DocBlox_Parallel_Manager
     */
    private $mgr = null;
    
    /**
     * Sets the maximum number of threads / processes to use
     * @param int $threadCount
     */
    public function setThreadCount($threadCount)
    {
        $this->mgr->setProcessLimit($threadCount);
    }
    
    public function init()
    {
        $this->mgr = new DocBlox_Parallel_Manager();
    }
    
    public function main()
    {
        foreach ($this->nestedTasks as $task) {
            $worker = new DocBlox_Parallel_Worker(
                function($task) { $task->perform(); },
                array($task)
            );
            
            $this->mgr->addWorker($worker);
        }
        
        $this->mgr->execute();
    }
}
