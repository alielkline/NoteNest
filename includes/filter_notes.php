<form method="GET" class="d-flex flex-wrap justify-content-center align-items-center gap-3">
    <!-- Filter by Classroom -->
    <div>
        <label for="classroomFilter" class="form-label custom-label me-2">Filter by Classroom:</label>
        <select name="classroom_id" id="classroomFilter" class="form-select custom-style d-inline-block w-auto ">
            <option value="all" <?= !isset($_GET['classroom_id']) || $_GET['classroom_id'] === 'all' ? 'selected' : '' ?>>All Classrooms</option>
            <?php foreach ($classrooms as $class): ?>
                <option value="<?= $class['classroom_id'] ?>" <?= (isset($_GET['classroom_id']) && $_GET['classroom_id'] == $class['classroom_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($class['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Sort by Upload Date -->
    <div>
        <label for="sortOrder" class="form-label custom-label me-2">Sort:</label>
        <select name="sort" id="sortOrder" class="form-select d-inline-block w-auto">
            <option value="newest" <?= (!isset($_GET['sort']) || $_GET['sort'] === 'newest') ? 'selected' : '' ?>>Newest</option>
            <option value="oldest" <?= (isset($_GET['sort']) && $_GET['sort'] === 'oldest') ? 'selected' : '' ?>>Oldest</option>
        </select>
    </div>

    <!-- Submit Button -->
    <div>
        <button type="submit" class="btn custom-style">
            <i class="bi bi-filter"></i> Apply
        </button>
    </div>
</form>