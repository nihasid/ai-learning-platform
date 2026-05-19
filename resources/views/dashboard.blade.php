<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0">Task Management Dashboard</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Tasks</li>
                </ol>
            </div>
        </div>
    </x-slot>

    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>24</h3>
                    <p>Open Tasks</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <a href="#task-board" class="small-box-footer">View board <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>68<sup style="font-size: 20px">%</sup></h3>
                    <p>Completion Rate</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <a href="#weekly-progress" class="small-box-footer">Track progress <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>7</h3>
                    <p>Due This Week</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <a href="#upcoming-deadlines" class="small-box-footer">Review dates <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>3</h3>
                    <p>Blocked Tasks</p>
                </div>
                <div class="icon">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <a href="#task-board" class="small-box-footer">Clear blockers <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row">
        <section class="col-lg-8 connectedSortable">
            <div class="card" id="task-board">
                <div class="card-header border-transparent">
                    <h3 class="card-title">Priority Tasks</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" aria-label="Collapse priority tasks">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0">
                            <thead>
                                <tr>
                                    <th>Task</th>
                                    <th>Owner</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Progress</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Review overdue client tasks</td>
                                    <td>{{ Auth::user()->name ?? 'Admin' }}</td>
                                    <td><span class="badge badge-warning">In Progress</span></td>
                                    <td><span class="task-priority-dot bg-danger"></span>High</td>
                                    <td>
                                        <div class="progress progress-sm task-progress">
                                            <div class="progress-bar bg-warning" style="width: 72%"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Prepare weekly task summary</td>
                                    <td>Operations</td>
                                    <td><span class="badge badge-info">Queued</span></td>
                                    <td><span class="task-priority-dot bg-warning"></span>Medium</td>
                                    <td>
                                        <div class="progress progress-sm task-progress">
                                            <div class="progress-bar bg-info" style="width: 45%"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Assign new project milestones</td>
                                    <td>Project Lead</td>
                                    <td><span class="badge badge-success">Ready</span></td>
                                    <td><span class="task-priority-dot bg-success"></span>Normal</td>
                                    <td>
                                        <div class="progress progress-sm task-progress">
                                            <div class="progress-bar bg-success" style="width: 90%"></div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Unblock vendor approval workflow</td>
                                    <td>Finance</td>
                                    <td><span class="badge badge-danger">Blocked</span></td>
                                    <td><span class="task-priority-dot bg-danger"></span>High</td>
                                    <td>
                                        <div class="progress progress-sm task-progress">
                                            <div class="progress-bar bg-danger" style="width: 20%"></div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer clearfix">
                    <button class="btn btn-sm btn-primary float-left" type="button">
                        <i class="fas fa-plus mr-1"></i> Add Task
                    </button>
                    <button class="btn btn-sm btn-secondary float-right" type="button">
                        <i class="fas fa-filter mr-1"></i> Filter Tasks
                    </button>
                </div>
            </div>

            <div class="card" id="weekly-progress">
                <div class="card-header">
                    <h3 class="card-title">Weekly Progress</h3>
                </div>
                <div class="card-body">
                    <p class="mb-2">Planning</p>
                    <div class="progress mb-3 task-progress">
                        <div class="progress-bar bg-primary" style="width: 85%"></div>
                    </div>
                    <p class="mb-2">Development</p>
                    <div class="progress mb-3 task-progress">
                        <div class="progress-bar bg-success" style="width: 64%"></div>
                    </div>
                    <p class="mb-2">Review</p>
                    <div class="progress mb-0 task-progress">
                        <div class="progress-bar bg-warning" style="width: 38%"></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="col-lg-4 connectedSortable">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Quick Task</h3>
                </div>
                <form>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="task-title">Title</label>
                            <input type="text" class="form-control" id="task-title" placeholder="Create onboarding checklist">
                        </div>
                        <div class="form-group">
                            <label for="task-owner">Owner</label>
                            <select class="form-control" id="task-owner">
                                <option>{{ Auth::user()->name ?? 'Admin' }}</option>
                                <option>Operations</option>
                                <option>Project Lead</option>
                                <option>Finance</option>
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label for="task-priority">Priority</label>
                            <select class="form-control" id="task-priority">
                                <option>Normal</option>
                                <option>Medium</option>
                                <option>High</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary btn-block">
                            <i class="fas fa-floppy-disk mr-1"></i> Save Draft Task
                        </button>
                    </div>
                </form>
            </div>

            <div class="card" id="upcoming-deadlines">
                <div class="card-header">
                    <h3 class="card-title">Upcoming Deadlines</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="products-list product-list-in-card pl-2 pr-2">
                        <li class="item">
                            <div class="product-img">
                                <span class="btn btn-sm btn-danger disabled">Today</span>
                            </div>
                            <div class="product-info">
                                <span class="product-title">Client handoff notes</span>
                                <span class="product-description">Finalize notes before end of day.</span>
                            </div>
                        </li>
                        <li class="item">
                            <div class="product-img">
                                <span class="btn btn-sm btn-warning disabled">Fri</span>
                            </div>
                            <div class="product-info">
                                <span class="product-title">Sprint planning</span>
                                <span class="product-description">Confirm scope and owners.</span>
                            </div>
                        </li>
                        <li class="item">
                            <div class="product-img">
                                <span class="btn btn-sm btn-info disabled">Mon</span>
                            </div>
                            <div class="product-info">
                                <span class="product-title">QA checklist</span>
                                <span class="product-description">Review test notes and release risks.</span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
