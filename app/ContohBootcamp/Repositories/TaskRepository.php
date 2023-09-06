<?php
namespace App\ContohBootcamp\Repositories;

use App\Helpers\MongoModel;

class TaskRepository
{
	private MongoModel $tasks;
	public function __construct()
	{
		$this->tasks = new MongoModel('tasks');
	}

	/**
	 * [GET COLLECTION DATAs] 
	 * Untuk mengambil semua tasks
	 */
	public function getAll()
	{
		$tasks = $this->tasks->get([]);
		return $tasks;
	}

	/**
	 * [GET DATA by ID] 
	 * Untuk mendapatkan task bedasarkan id
	 *  */
	public function getById(string $id)
	{
		$task = $this->tasks->find(['_id' => $id]);
		return $task;
	}

	/**
	 * [SAVE NEW or UPDATED DATA] 
	 * Untuk menyimpan task baik untuk membuat baru atau perubahan task yang sudah ada
	 *  */
	public function save(array $data)
	{
		$id = $this->tasks->save($data);
		return $id;
	}

	/**
	 * [DELETE DATA by ID] 
	 * Untuk menghapus data task berdasarkan id
	 */
	public function delete(string $id)
	{
		$id = $this->tasks->deleteQuery(['_id' => $id]);
		return $id;
	}
}