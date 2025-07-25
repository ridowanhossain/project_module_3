
<?php
const TASKS_FILE = 'tasks.json';

// this function saves tasks to json file
function saveTasks(array $tasks): void
{
    file_put_contents(TASKS_FILE, json_encode($tasks, JSON_PRETTY_PRINT));
}

// this function loads tasks from json file
function loadTasks(): array
{
    if (!file_exists(TASKS_FILE)) {
        return [];
    }
    
    $data = file_get_contents(TASKS_FILE);
    $tasks = json_decode($data, true);
    
    return $tasks ? $tasks : [];
}

// Load existing tasks
$tasks = loadTasks();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Handle adding a new task
    if (isset($_POST['task']) && !empty(trim($_POST['task']))) {
        $taskText = trim($_POST['task']);
        $taskText = htmlspecialchars($taskText);
        
        $newTask = [
            'task' => $taskText,
            'done' => false
        ];
        
        // Add new task at beginning so newest appears first
        array_unshift($tasks, $newTask);
        
        saveTasks($tasks);
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    
    // Handle toggling task done/undone
    if (isset($_POST['toggle'])) {
        $taskIndex = (int)$_POST['toggle'];
        
        // Check if task index exists
        if (isset($tasks[$taskIndex])) {
            $tasks[$taskIndex]['done'] = !$tasks[$taskIndex]['done'];
        }
        
        saveTasks($tasks);
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    
    // Handle deleting a task
    if (isset($_POST['delete'])) {
        $taskIndex = (int)$_POST['delete'];
        
        // Check if task index exists and remove it
        if (isset($tasks[$taskIndex])) {
            unset($tasks[$taskIndex]);
            $tasks = array_values($tasks); // Re-index array
        }
        
        saveTasks($tasks);
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

<!-- UI -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.min.css">
    <style>
        body {
            margin-top: 20px;
        }
        .task-card {
            border: 1px solid #ececec; 
            padding: 20px;
            border-radius: 5px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); 
        }
        .task{
            color: #888;
        }
        .task-done {
            text-decoration: line-through;
            color: #888;
        }
        .task-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        ul {
            padding-left: 20px;
        }
        button {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="task-card">
            <h1>To-Do App</h1>

            <!-- Add Task Form -->
            <form method="POST">
                <div class="row">
                    <div class="column column-75">
                        <input type="text" name="task" placeholder="Enter a new task" required>
                    </div>
                    <div class="column column-25">
                        <button type="submit" class="button-primary">Add Task</button>
                    </div>
                </div>
            </form>

            <!-- Task List -->
            <h2>Task List</h2>
            <ul style="list-style: none; padding: 0;">
                <?php if (empty($tasks)): ?>
                    <li>No tasks yet. Add one above!</li>
                <?php else: ?>
                    <?php foreach ($tasks as $index => $task): ?>
                        <li class="task-item">
                            <form method="POST" style="flex-grow: 1;">
                                <input type="hidden" name="toggle" value="<?php echo $index; ?>">
                                <button type="submit" style="border: none; background: none; cursor: pointer; text-align: left; width: 100%;">
                                    <span class="<?php echo $task['done'] ? 'task-done' : 'task'; ?>">
                                        <?php echo $task['task']; ?>
                                    </span>
                                </button>
                            </form>

                            <form method="POST">
                                <input type="hidden" name="delete" value="<?php echo $index; ?>">
                                <button type="submit" class="button button-outline" style="margin-left: 10px;">Delete</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>

            </ul>

        </div>
    </div>
</body>
</html>