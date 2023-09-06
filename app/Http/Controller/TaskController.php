<?php

namespace App\Http\Controller;

use App\ContohBootcamp\Services\TaskService;
use App\Helpers\MongoModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TaskController extends Controller {
	private TaskService $taskService;
	public function __construct() {
		$this->taskService = new TaskService();
	}

	// TODO: showTask()
	public function showTasks()
	{
		$tasks = $this->taskService->getTasks();
		return response()->json($tasks);
	}

	// TODO: createTask()
	public function createTask(Request $request)
	{
		$request->validate([
			'title'			=> 'required|string|min:3',
			'description'	=> 'required|string'
		]);

		$dataSaved = [
			'title'			=> $request->post('title'),
			'description'	=> $request->post('description'),
			'assigned'		=> null,
			'subtasks'		=> [],
			'created_at'	=> time()
		];

		$result = $this->taskService->addTask($dataSaved);

		return response()->json($result);
	}

	// TODO: updateTask()
	public function updateTask(Request $request)
	{
		$request->validate([
			'task_id'		=> 'required|string',
			'title'			=> 'string',
			'description'	=> 'string',
			'assigned'		=> 'string',
			'subtasks'		=> 'array',
		]);

		$taskId  = $request->task_id;
		$newData = $request->only('title', 'description', 'assigned', 'subtasks');
		$result  = $this->taskService->updateData($taskId, $newData);

		return response()->json($result["result"], $result["rsCode"]);
	}


	// TODO: deleteTask()
	public function deleteTask(Request $request)
	{
		$request->validate([
			'task_id' => 'required'
		]);

		$taskId = $request->task_id;
		$result = $this->taskService->deleteData($taskId);

		return response()->json($result["result"], $result["rsCode"]);
	}
	
	// TODO: assignTask()
	public function assignTask(Request $request)
	{
		$request->validate([
			'task_id'	=> 'required',
			'assigned'	=> 'required'
		]);
		
		$taskId 	= $request->get('task_id');
		$assigned 	= $request->post('assigned');
		$result 	= $this->taskService->taskAssignment($taskId, $assigned);
		
		return response()->json($result["result"], $result["rsCode"]);
	}

	// TODO: unassignTask()
	public function unassignTask(Request $request)
	{
		$request->validate([
			'task_id' => 'required'
		]);

		$taskId = $request->post('task_id');
		/**
		 * untuk membebas tugaskan (nama) orang dari suatu task, ..
		 * .. panggil fungsi taskAssignment dengan mengoper task_id tanpa nama orang (assigned person).
		 */
		$result = $this->taskService->taskAssignment($taskId);
		
		return response()->json($result["result"], $result["rsCode"]);
	}

	// TODO: createSubtask()
	public function createSubtask(Request $request)
	{
		$request->validate([
			'task_id'		=> 'required',
			'title'			=> 'required|string',
			'description'	=> 'required|string'
		]);

		$taskId 		= $request->post('task_id');
		$title 			= $request->post('title');
		$description 	= $request->post('description');

		$newSubTask = [
			'_id'			=> (string) new \MongoDB\BSON\ObjectId(),
			'title'			=> $title,
			'description'	=> $description
		];

		$result = $this->taskService->addSubTask($taskId, $newSubTask);
		
		return response()->json($result["result"], $result["rsCode"]);
	}

	// TODO deleteSubTask()
	public function deleteSubtask(Request $request)
	{
		$mongoTasks = new MongoModel('tasks');
		$request->validate([
			'task_id'		=> 'required',
			'subtask_id'	=> 'required'
		]);

		$taskId 	= $request->post('task_id');
		$subTaskId 	= $request->post('subtask_id');

		$result = $this->taskService->removeSubTask($taskId, $subTaskId);
		
		return response()->json($result["result"], $result["rsCode"]);
	}

}