<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Jobs\GenerateScheduleJob;
use App\Models\Task;
// use App\Models\TaskFeedback;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tasks = $request->user()->tasks()
            ->with(['category'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->category, fn ($q) => $q->where('category_id', $request->category))
            ->when($request->search, fn ($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->orderBy('ai_rank')
            ->paginate(20)
            ->withQueryString();
     
        $categories = $request->user()->categories()->get();
     
        return view('tasks.index', compact('tasks', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $categories = $request->user()->categories()->get();
        return view('tasks.create', compact('categories'));
    }

    
    public function store(StoreTaskRequest $request)
    {
        $task = $request->user()->tasks()->create($request->validated());
 
        // Queue AI re-ranking after new task added
        // GenerateScheduleJob::dispatch($request->user())->delay(now()->addSeconds(5));
 
        return redirect()->route('tasks.show', $task)
            ->with('success', 'Task created! AI is re-ranking your schedule.');
    }
 
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        $task->load(['category', 'schedules', 'subtasks', 'feedback']);
        return view('tasks.show', compact('task'));
    }
 
    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        $categories = $task->user->categories()->get();
        return view('tasks.edit', compact('task', 'categories'));
    }
 
    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);
        $task->update($request->validated());
 
        // GenerateScheduleJob::dispatch($request->user())->delay(now()->addSeconds(5));
 
        return redirect()->route('tasks.show', $task)->with('success', 'Task updated.');
    }
 
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted.');
    }
 
    // Mark a task as complete and record feedback
    /*public function complete(Request $request, Task $task)
    {
        $this->authorize('update', $task);
 
        $task->update([
            'status'       => 'completed',
            'completed_at' => now(),
            'actual_minutes' => $request->actual_minutes,
        ]);
 
        TaskFeedback::create([
            'task_id' => $task->id,
            'user_id' => $request->user()->id,
            'action'  => 'completed',
            'rating'  => $request->rating,
            'note'    => $request->note,
        ]);
 
        GenerateScheduleJob::dispatch($request->user());
 
        return back()->with('success', 'Great work! Task marked complete.');
    }*/
 
    // Snooze a task — AI will reschedule it
   /* public function snooze(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        $task->update(['status' => 'snoozed']);
 
        TaskFeedback::create([
            'task_id' => $task->id,
            'user_id' => $request->user()->id,
            'action'  => 'snoozed',
        ]);
 
        GenerateScheduleJob::dispatch($request->user());
        return back()->with('info', 'Task snoozed. AI will find a better time.');
    }*/
}
