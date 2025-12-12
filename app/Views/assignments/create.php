<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="text-white mb-2">Create New Assignment</h2>
            <p class="text-muted">Course: <?= esc($course['title']) ?></p>
        </div>
    </div>

    <div class="card bg-dark text-light border-0 shadow-sm">
        <div class="card-body">
            <form id="createAssignmentForm" enctype="multipart/form-data">
                <input type="hidden" name="course_id" value="<?= esc($course['id']) ?>">

                <div class="mb-3">
                    <label for="title" class="form-label">Assignment Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Instructions / Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="description" name="description" rows="6" required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="due_date" class="form-label">Due Date (Optional)</label>
                        <input type="datetime-local" class="form-control" id="due_date" name="due_date">
                        <small class="form-text text-muted">Leave blank if no due date</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="max_score" class="form-label">Max Score <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="max_score" name="max_score" step="0.01" min="0" max="999.99" value="100.00" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="draft">Draft</option>
                        <option value="published" selected>Published</option>
                    </select>
                </div>

                <!-- Questions Section -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 text-light">Questions</h5>
                        <button type="button" class="btn btn-success" id="add-question-btn">
                            <i class="fas fa-plus"></i> Add Question
                        </button>
                    </div>
                    <div id="questions-container">
                        <!-- Empty state when no questions exist -->
                        <div id="empty-questions-state" class="text-center py-4">
                            <i class="fas fa-question-circle fa-2x text-muted mb-3"></i>
                            <p class="text-muted mb-2">No questions added yet</p>
                            <small class="text-muted">Click "Add Question" above to start building your assignment</small>
                        </div>
                        <!-- Questions will be added here dynamically -->
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('assignment/teacher-view/' . $course['id']) ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save"></i> Create Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
let questionCount = 0;

$(document).ready(function() {
    // Add Question Button Click Handler
    $('#add-question-btn').on('click', function() {
        addQuestion();
    });

    // Question Type Change Handler
    $(document).on('change', '.question-type-select', function() {
        const questionContainer = $(this).closest('.question-container');
        const questionType = $(this).val();
        updateQuestionForm(questionContainer, questionType);
    });

    // Remove Question Button Click Handler
    $(document).on('click', '.remove-question-btn', function() {
        const questionContainer = $(this).closest('.question-container');
        if (confirm('Are you sure you want to remove this question?')) {
            questionContainer.remove();
            updateQuestionNumbers();
        }
    });

    // Form Submission
    $('#createAssignmentForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const questions = collectQuestionsData();

        formData.append('questions', JSON.stringify(questions));

        const submitBtn = $('#submitBtn');

        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Creating...');

        $.ajax({
            url: '<?= base_url('assignment/store') ?>',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    window.location.href = '<?= base_url('assignment/teacher-view/' . $course['id']) ?>';
                } else {
                    let errorMsg = response.message || 'Failed to create assignment';
                    if (response.errors) {
                        errorMsg += '\n' + Object.values(response.errors).join('\n');
                    }
                    alert(errorMsg);
                    submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Create Assignment');
                }
            },
            error: function() {
                alert('An error occurred while creating the assignment');
                submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Create Assignment');
            }
        });
    });

    function addQuestion() {
        questionCount++;
        const questionHtml = `
            <div class="card mb-3 question-container" data-question-id="${questionCount}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Question ${questionCount}</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger remove-question-btn">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Question Type</label>
                            <select class="form-control question-type-select" name="question_type_${questionCount}" required>
                                <option value="">Select Type</option>
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="essay">Essay</option>
                                <option value="file">File</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Points</label>
                            <input type="number" class="form-control" name="max_points_${questionCount}" step="0.01" min="0" value="10.00" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Question Text</label>
                        <textarea class="form-control" name="question_text_${questionCount}" rows="3" placeholder="Enter your question..." required></textarea>
                    </div>
                    <div class="question-specific-fields"></div>
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>This question will be manually graded by the teacher.
                    </small>
                </div>
            </div>
        `;
        $('#questions-container').append(questionHtml);

        // Hide empty state when we add the first question
        if ($('#empty-questions-state').is(':visible')) {
            $('#empty-questions-state').hide();
        }
    }

    function updateQuestionForm(questionContainer, questionType) {
        const specificFieldsContainer = questionContainer.find('.question-specific-fields');
        const questionId = questionContainer.data('question-id');

        specificFieldsContainer.empty();

        if (questionType === 'multiple_choice') {
            const html = `
                <div class="mb-3">
                    <label class="form-label">Option A</label>
                    <input type="text" class="form-control" name="option_${questionId}_A" placeholder="Enter option A..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Option B</label>
                    <input type="text" class="form-control" name="option_${questionId}_B" placeholder="Enter option B..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Option C</label>
                    <input type="text" class="form-control" name="option_${questionId}_C" placeholder="Enter option C..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Option D</label>
                    <input type="text" class="form-control" name="option_${questionId}_D" placeholder="Enter option D..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Correct Answer</label>
                    <select class="form-control" name="correct_answer_${questionId}" required>
                        <option value="">Select correct answer</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                    </select>
                </div>
            `;
            specificFieldsContainer.html(html);
        } else if (questionType === 'essay') {
            // No additional fields needed for essay - just question text and points
        } else if (questionType === 'file') {
            const html = `
                <div class="mb-3">
                    <label class="form-label">Attachment (Optional)</label>
                    <input type="file" class="form-control" name="question_file_${questionId}" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.zip">
                    <small class="text-muted">Allowed: PDF, DOC, DOCX, PPT, PPTX, TXT, ZIP (Max 10MB)</small>
                </div>
            `;
            specificFieldsContainer.html(html);
        }
    }





    function updateQuestionNumbers() {
        $('.question-container').each(function(index) {
            $(this).find('h6').text(`Question ${index + 1}`);
        });

        // Show empty state if no questions remain
        if ($('.question-container').length === 0) {
            $('#empty-questions-state').show();
        }
    }

    function collectQuestionsData() {
        const questions = [];
        $('.question-container').each(function(index) {
            const questionContainer = $(this);
            const questionId = questionContainer.data('question-id');
            const questionType = questionContainer.find('.question-type-select').val();
            const maxPoints = questionContainer.find(`input[name="max_points_${questionId}"]`).val();
            const questionText = questionContainer.find(`textarea[name="question_text_${questionId}"]`).val();

            const question = {
                question_type: questionType,
                question_text: questionText,
                max_points: maxPoints,
                order_position: index + 1
            };

            if (questionType === 'multiple_choice') {
                const correctAnswer = questionContainer.find(`select[name="correct_answer_${questionId}"]`).val();
                const options = [
                    {
                        text: questionContainer.find(`input[name="option_${questionId}_A"]`).val(),
                        is_correct: correctAnswer === 'A'
                    },
                    {
                        text: questionContainer.find(`input[name="option_${questionId}_B"]`).val(),
                        is_correct: correctAnswer === 'B'
                    },
                    {
                        text: questionContainer.find(`input[name="option_${questionId}_C"]`).val(),
                        is_correct: correctAnswer === 'C'
                    },
                    {
                        text: questionContainer.find(`input[name="option_${questionId}_D"]`).val(),
                        is_correct: correctAnswer === 'D'
                    }
                ];
                question.options = options;
            }

            questions.push(question);
        });
        return questions;
    }
});
</script>
<?= $this->endSection() ?>
