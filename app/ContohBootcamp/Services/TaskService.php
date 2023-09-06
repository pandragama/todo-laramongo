<?php

namespace App\ContohBootcamp\Services;

use App\ContohBootcamp\Repositories\TaskRepository;

class TaskService {
	private TaskRepository $taskRepository;

	public function __construct() {
		$this->taskRepository = new TaskRepository();
	}

	/**
	 * NOTE: Fungsi untuk mengambil semua data tasks di collection task.
	 */
	public function getTasks()
	{
		$tasks = $this->taskRepository->getAll();
		return $tasks;
	}

	/**
	 * NOTE: menambahkan data task baru
	 */
	public function addTask(array $data)
	{
		$id = $this->taskRepository->save($data);

		$newData = $this->taskRepository->getById($id);
		
		return $newData;
	}

	/**
	 * NOTE: Fungsi untuk update data task.
	 */
	public function updateData(string $taskId, array $newData)
	{
		$existingData = $this->taskRepository->getById($taskId);

		// jika data dengan id yang dimaksud tidak ditemukan
		if(!$existingData)
		{
			return array(
				"result" => ["message" => "[ERROR] Data Task dengan id-" . $taskId . " tidak ditemukan."],
				"rsCode" => 401
			);
		}

		if(isset($newData['title']))
		{
			$existingData['title'] = $newData['title'];
		}

		if(isset($newData['description']))
		{
			$existingData['description'] = $newData['description'];
		}

		$id = $this->taskRepository->save($existingData);

		$updatedData = $this->taskRepository->getById($id);

		return array(
			"result" => $updatedData,
			"rsCode" => 200
		);
	}

	/**
	 * NOTE: Fungsi untuk menghapus data task.
	 */
	public function deleteData(string $taskId)
	{
		$existingData = $this->taskRepository->getById($taskId);

		if(!$existingData)
		{
			return array(
				"result" => ["message" => "[ERROR] Data Task dengan id-" . $taskId . " tidak ditemukan."],
				"rsCode" => 401
			);
		}

		$this->taskRepository->delete($taskId);

		return array(
			"result" => ["message" => "[SUCCESS] Data Task dengan id-" . $taskId . " berhasil dihapus."],
			"rsCode" => 401
		);
	}

	/**
	 * NOTE: Fungsi untuk mengubah nama orang yang ditugaskan pada suatu task berdasarkan id task.
	 * 		 Jika fungsi ini tidak menerima nilai untuk parameter $assigned, ..
	 * 		 .. maka "assigned" pada data task akan diisi dengan nilai default yaitu NULL.
	 */
	public function taskAssignment(string $taskId, string $assigned = NULL)
	{
		$existingData = $this->taskRepository->getById($taskId);

		if(!$existingData)
		{
			return array(
				"result" => ["message" => "[ERROR] Data Task dengan id-" . $taskId . " tidak ditemukan."],
				"rsCode" => 401
			);
		}
	
		$existingData['assigned'] = $assigned;
	
		$id = $this->taskRepository->save($existingData);
	
		$updatedData = $this->taskRepository->getById($id);

		return array(
			"result" => $updatedData,
			"rsCode" => 200
		);
	}

	/**
	 * NOTE: Fungsi untuk menambahkan subtask ke sebuah task berdasarkan id
	 */
	public function addSubTask(string $taskId, array $data)
	{
		$existingData = $this->taskRepository->getById($taskId);

		if(!$existingData)
		{
			return array(
				"result" => ["message" => "[ERROR] Data Task dengan id-" . $taskId . " tidak ditemukan."],
				"rsCode" => 401
			);
		}
		
		$subTasks = isset($existingData['subtasks']) ? $existingData['subtasks'] : [];

		$subTasks[] = $data;

		$existingData['subtasks'] = $subTasks;

		$id = $this->taskRepository->save($existingData);
	
		$updatedData = $this->taskRepository->getById($id);

		return array(
			"result" => $updatedData,
			"rsCode" => 200
		);
	}

	/**
	 * NOTE: Fungsi untuk menghapus sebuah subtask (berdasarkan id) dari suatu task berdasarkan id
	 */
	public function removeSubTask(string $taskId, string $subTaskId)
	{
		$existingData = $this->taskRepository->getById($taskId);
		$subTask_isExist = 0;

		if(!$existingData)
		{
			return array(
				"result" => ["message" => "[ERROR] Data Task dengan id-" . $taskId . " tidak ditemukan."],
				"rsCode" => 401
			);
		}

		$subTasks = isset($existingData['subtasks']) ? $existingData['subtasks'] : [];

		/**
		 * Memisahkan data-data subtasks dari data subtask yang hendak dihapus.
		 * 
		 * array_filter() akan melakukan iterasi setiap nilai dalam array dan meneruskannya ke fungsi "anonymous". 
		 * Lalu, jika fungsi "anonymous" mengembalikan nilai true, nilai saat ini dari array dikembalikan ke array hasil ($subTasks).
		 */
		$subTasks = array_filter($subTasks, function($subTask) use($subTaskId, &$subTask_isExist) {
			if($subTask['_id'] == $subTaskId)
			{
				$subTask_isExist = 1;
				return false;
			}
			else return true;
		});

		// jika subtask tidak ditemukan
		if(!$subTask_isExist)
		{
			return array(
				"result" => ["message" => "[ERROR] Data SubTask dengan id-" . $subTaskId . " tidak ditemukan dalam Task yang dimaksud (" . $taskId . ")."],
				"rsCode" => 401
			);
		}

		/**
		 * Setelah difilter atau dihapusnya 1 nilai (indeks) -nya, urutan indeks nilai dalam array subtasks menjadi tidak sesuai urutan nemerik.
		 * array_values() akan mengembalikan semua nilai dari array dan mengindeks array secara numerik.
		 */
		$existingData['subtasks'] = array_values($subTasks);

		$id = $this->taskRepository->save($existingData);
	
		$updatedData = $this->taskRepository->getById($id);

		return array(
			"result" => $updatedData,
			"rsCode" => 200
		);
	}

}