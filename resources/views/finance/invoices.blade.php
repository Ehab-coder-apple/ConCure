<!DOCTYPE html>
<html>
<head>
    <title>Finance - Invoices</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Finance - Invoices</h1>
        <div class="alert alert-success">
            <strong>Success!</strong> The finance invoices view is now working.
        </div>
        
        <div class="card">
            <div class="card-body">
                <h5>Invoice Management</h5>
                <p>This is the invoices page. The view file has been successfully created.</p>
                
                @if(isset($invoices))
                    <p>Invoices data is available: {{ $invoices->count() ?? 0 }} invoices found.</p>
                @else
                    <p>No invoices data passed to view.</p>
                @endif
                
                <a href="{{ route('finance.index') }}" class="btn btn-primary">Back to Finance Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>
