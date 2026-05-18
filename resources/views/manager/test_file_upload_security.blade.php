<!DOCTYPE html>
<html>
<head>
    <title>File Upload Security Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .test-section {
            background: #f5f5f5;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .test-section h3 {
            color: #333;
            margin-top: 0;
        }
        .result {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        input[type="file"] {
            margin: 10px 0;
        }
        button {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <h1>🔒 File Upload Security Test</h1>
    <p>This page tests the security improvements made to file uploads. Test each scenario below:</p>

    <!-- Test 1: Valid Image Upload -->
    <div class="test-section">
        <h3>✅ Test 1: Valid Image Upload (JPEG/PNG)</h3>
        <p>Expected: Should upload successfully</p>
        <form id="test1" action="{{ route('manager.standarditem.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="text" name="item_name" value="Test Item" required hidden>
            <input type="text" name="item_code" value="TEST001" required hidden>
            <input type="text" name="category_id" value="1" required hidden>
            <input type="number" name="cost_price" value="100" required hidden>
            <input type="text" name="pricing_type" value="fixed" required hidden>
            <input type="number" name="selling_price" value="150" required hidden>

            <input type="file" name="item_image" accept="image/*">
            <button type="submit">Test Valid Upload</button>
        </form>
        <div id="result1" class="result" style="display:none;"></div>
    </div>

    <!-- Test 2: PHP File Upload (Should Fail) -->
    <div class="test-section">
        <h3>🚫 Test 2: PHP File Upload</h3>
        <p>Expected: Should be REJECTED with validation error</p>
        <div class="warning">
            <strong>How to test:</strong> Create a text file named "test.php" with content: <code>&lt;?php echo "test"; ?&gt;</code>
            Then try to upload it using the form above.
        </div>
        <p>If you can upload a .php file, the security fix has FAILED.</p>
    </div>

    <!-- Test 3: Large File Upload (Should Fail) -->
    <div class="test-section">
        <h3>📏 Test 3: Large File Upload (>2MB)</h3>
        <p>Expected: Should be REJECTED with validation error</p>
        <div class="warning">
            <strong>How to test:</strong> Try to upload an image larger than 2MB (2048KB)
        </div>
    </div>

    <!-- Test 4: EXE File Upload (Should Fail) -->
    <div class="test-section">
        <h3>⚠️ Test 4: Executable File Upload</h3>
        <p>Expected: Should be REJECTED with validation error</p>
        <div class="warning">
            <strong>How to test:</strong> Try to upload a .exe or .bat file
        </div>
    </div>

    <!-- Test 5: Image Display Test -->
    <div class="test-section">
        <h3>🖼️ Test 5: Image Display Test</h3>
        <p>Check if uploaded images display correctly:</p>
        <ul>
            <li><a href="{{ route('manager.sell_product') }}">Go to Sell Product Page</a></li>
            <li>Verify NEW uploads show correctly (from storage/app/public)</li>
            <li>Verify OLD uploads still show correctly (from public/uploads)</li>
        </ul>
    </div>

    <!-- Security Checklist -->
    <div class="test-section">
        <h3>📋 Security Checklist</h3>
        <ul>
            <li>✅ File extension validation (mimes:jpeg,png,jpg,gif,webp)</li>
            <li>✅ File size validation (max:2048KB for images)</li>
            <li>✅ MIME type verification (Laravel's built-in check)</li>
            <li>✅ User-provided filename discarded (Laravel generates safe random name)</li>
            <li>✅ Files stored securely (storage/app/public with symlink)</li>
            <li>⚠️ Virus scanning NOT implemented (requires external service)</li>
        </ul>
    </div>

    <!-- Backend Validation Test Results -->
    <div class="test-section">
        <h3>🔍 Backend Validation Rules</h3>
        <pre>
// StandardItem & VariantItem
'item_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'

// Staff Passport Photo
'passport_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'

// Business Logo
'business_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120'
        </pre>
        <p><strong>Storage Method:</strong> <code>$request->file('item_image')->store('item_images', 'public')</code></p>
        <p><strong>Result:</strong> Files stored in <code>storage/app/public/item_images/</code> with random safe filenames</p>
    </div>

    @if(session('success'))
        <div class="result success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="result error">
            <strong>Validation Errors:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <script>
        // Add AJAX submission for test 1
        document.getElementById('test1').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const result = document.getElementById('result1');

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                result.style.display = 'block';
                if(data.success) {
                    result.className = 'result success';
                    result.textContent = '✅ Upload successful! File stored securely.';
                } else {
                    result.className = 'result error';
                    result.textContent = '❌ Upload failed: ' + (data.message || 'Unknown error');
                }
            })
            .catch(error => {
                result.style.display = 'block';
                result.className = 'result error';
                result.textContent = '❌ Error: ' + error.message;
            });
        });
    </script>
</body>
</html>
