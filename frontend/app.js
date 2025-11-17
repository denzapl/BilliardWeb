// --- 1. CONFIGURATION AND INITIALIZATION ---
const API_BASE_URL = 'http://127.0.0.1:8080/flightphp_project/backend/api/v1';
const contentArea = document.getElementById('main-content');

// NOTE: MOCK_USER_DATA removed. Data will be fetched from the backend.

// Utility function to load categories (used by both Create and Edit Club views)
async function loadCategories(selectedValue = 0) {
    try {
        const response = await fetch(`${API_BASE_URL}/categories`);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        
        const data = await response.json();
        const select = document.getElementById('category_id');
        
        select.innerHTML = '<option value="0">Select a Category (Optional)</option>'; 
        
        if (data.success && data.data) {
            data.data.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.category_name;
                if (parseInt(category.id) === parseInt(selectedValue)) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        } else {
             console.warn('API returned success=false or empty data for categories.');
             // Keep default option if API fails
        }
    } catch (error) {
        console.error('Error loading categories:', error);
        const select = document.getElementById('category_id');
        select.innerHTML = '<option value="0">Network Error - Cannot load categories</option>'; 
    }
}


// --- 2. ROUTING ---
const routes = {
    '': homeView,
    'clubs': clubsListView,
    'clubs/create': clubCreateView,
    'categories': categoriesListView,
    'categories/create': categoryCreateView,
    'profile': profileView, // Profile View Route
};

function router() {
    let hash = window.location.hash.slice(1) || '';
    
    if (hash === '/') {
        hash = '';
    }

    // 1. Check for specific static routes first
    let handler = routes[hash];
    
    // 2. Check for dynamic routes (clubs/ID, categories/ID, clubs/ID/edit, etc.)
    if (!handler) {
        const parts = hash.split('/');
        
        const type = parts[0];
        const id = parts[1];
        const action = parts[2];

        // Ensure ID is numeric for detail/edit views
        if (parts.length >= 2 && /^\d+$/.test(id)) {
            if (type === 'clubs') {
                if (action === 'edit') {
                    clubEditView(id);
                    return;
                } else if (!action) {
                    clubDetailView(id);
                    return;
                }
            } else if (type === 'categories') {
                if (action === 'edit') {
                    categoryEditView(id);
                    return;
                } else if (!action) {
                    categoryDetailView(id);
                    return;
                }
            }
        }
    }
    
    // 3. Execute static handler or show 404
    if (handler) {
        handler();
    } else {
        contentArea.innerHTML = '<h1>404</h1><p>Page not found.</p>';
    }
}

// Listen for hash changes
window.addEventListener('hashchange', router);
// Load the initial view
window.addEventListener('load', router);


// --- 3. VIEW FUNCTIONS ---

function homeView() {
    contentArea.innerHTML = `
        <div class="p-5 bg-light rounded-3 shadow-sm">
            <h2 class="display-4 text-primary">Welcome to the Billiard Club Manager</h2>
            <p class="lead">Use the navigation above to manage billiard clubs and categories across your network. Start by viewing the <a href="#clubs">Clubs List</a>.</p>
        </div>
    `;
}

// Club List View (No changes needed here as buttons were added in the previous step)
async function clubsListView() {
    contentArea.innerHTML = '<h2>Billiard Clubs</h2><p>Loading...</p>';
    
    try {
        const response = await fetch(`${API_BASE_URL}/clubs`);
        const result = await response.json();

        if (result.success && result.data) {
            let html = `
                <a href="#clubs/create" class="btn btn-success mb-3">Create New Club</a>
                <table id="clubs-table" class="table table-striped table-bordered align-middle">
                    <thead><tr><th>ID</th><th>Owner</th><th>Name</th><th>Address</th><th>Members</th><th>Actions</th></tr></thead>
                    <tbody>
            `;
            result.data.forEach(club => {
                html += `
                    <tr>
                        <td>${club.id}</td>
                        <td>${club.owner_name}</td>
                        <td>${club.club_name}</td>
                        <td>${club.address}</td>
                        <td>${club.club_member_count || 0}</td>
                        <td class="d-flex gap-2">
                            <a href="#clubs/${club.id}" class="btn btn-sm btn-info text-white">Show</a>
                            <a href="#clubs/${club.id}/edit" class="btn btn-sm btn-warning">Edit</a>
                            <button class="btn btn-sm btn-danger" onclick="window.deleteClub(${club.id})">Delete</button>
                        </td>
                    </tr>
                `;
            });
            html += `</tbody></table>`;
            
            contentArea.innerHTML = html;
        } else {
            contentArea.innerHTML = `<div class="alert alert-danger">Error loading clubs: ${result.message || 'API error.'}</div>`;
        }
    } catch (error) {
        console.error('Network Error:', error);
        contentArea.innerHTML = `<div class="alert alert-danger">Network Error: Cannot connect to the API. Check console for details.</div>`;
    }
}

// Club Detail View
async function clubDetailView(id) {
    contentArea.innerHTML = `<h2>Club Detail (ID: ${id})</h2><p>Loading...</p>`;
    
    try {
        const response = await fetch(`${API_BASE_URL}/clubs/${id}`);
        const result = await response.json();

        if (result.success && result.data) {
            const club = result.data;
            let html = `
                <div class="card p-4 shadow-lg">
                    <h3 class="card-title text-primary">${club.club_name}</h3>
                    <h4 class="card-subtitle text-muted mb-3">Owned by ${club.owner_name}</h4>
                    <p><strong>Address:</strong> ${club.address || 'N/A'}</p>
                    <p><strong>Members:</strong> ${club.club_member_count || 0}</p>
                    <p><strong>Category ID:</strong> ${club.category_id || 'None'}</p>
                    <div class="mt-3 d-flex gap-2">
                        <a href="#clubs/${club.id}/edit" class="btn btn-warning"><i class="fas fa-edit me-1"></i> Edit Club</a>
                        <a href="#clubs" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Back to List</a>
                    </div>
                </div>
            `;
            contentArea.innerHTML = html;
        } else {
            contentArea.innerHTML = `<div class="alert alert-danger">Error loading club: ${result.message || 'Club not found or API error.'}</div>`;
        }
    } catch (error) {
        console.error('Network Error:', error);
        contentArea.innerHTML = `<div class="alert alert-danger">Network Error: Could not load club details.</div>`;
    }
}

// Club Create View (No changes needed here)
function clubCreateView() {
    contentArea.innerHTML = `
        <h1 class="mb-4">Create New Billiard Club</h1>
        <div id="createClubFormContainer" class="card p-4">Loading form...</div>
    `;

    fetch('frontend/views/clubs/create.html')
        .then(response => {
            if (!response.ok) throw new Error('Failed to load form template');
            return response.text();
        })
        .then(html => {
            document.getElementById('createClubFormContainer').innerHTML = html;
            setupCreateClubView();
        })
        .catch(err => {
            console.error(err);
            document.getElementById('createClubFormContainer').innerHTML = `<div class="alert alert-danger">Error loading form assets.</div>`;
        });
}

// Club Edit View
async function clubEditView(id) {
    contentArea.innerHTML = `<h1 class="mb-4">Edit Billiard Club (ID: ${id})</h1><div id="editClubFormContainer" class="card p-4">Loading club data...</div>`;
    const formContainer = document.getElementById('editClubFormContainer');

    try {
        const clubResponse = await fetch(`${API_BASE_URL}/clubs/${id}`);
        const clubResult = await clubResponse.json();

        if (!clubResult.success) {
            formContainer.innerHTML = `<div class="alert alert-danger">Error fetching club data: ${clubResult.message || 'Club not found.'}</div>`;
            return;
        }

        const clubData = clubResult.data;

        // Load the edit form template
        const templateResponse = await fetch('frontend/views/clubs/edit.html');
        if (!templateResponse.ok) throw new Error('Failed to load edit form template');
        const html = await templateResponse.text();
        formContainer.innerHTML = html;

        // Populate form fields
        document.getElementById('clubId').value = clubData.id;
        document.getElementById('club_name').value = clubData.club_name;
        document.getElementById('owner_name').value = clubData.owner_name;
        document.getElementById('address').value = clubData.address;
        document.getElementById('club_member_count').value = clubData.club_member_count || 0;
        
        // Setup form submission and load categories with current selection
        setupEditClubView(id);
        await loadCategories(clubData.category_id);


    } catch (error) {
        console.error('Club Edit View Error:', error);
        formContainer.innerHTML = `<div class="alert alert-danger">Network or loading error: ${error.message}</div>`;
    }
}


// Category List View
async function categoriesListView() {
    contentArea.innerHTML = '<h2>Billiard Categories</h2><p>Loading...</p>';
    
    try {
        const response = await fetch(`${API_BASE_URL}/categories`);
        const result = await response.json();

        if (result.success && result.data) {
            let html = `
                <a href="#categories/create" class="btn btn-success mb-3">Create New Category</a>
                <table id="categories-table" class="table table-striped table-bordered align-middle">
                    <thead><tr><th>ID</th><th>Category Name</th><th>Actions</th></tr></thead>
                    <tbody>
            `;
            result.data.forEach(category => {
                html += `
                    <tr>
                        <td>${category.id}</td>
                        <td>${category.category_name}</td>
                        <td class="d-flex gap-2">
                            <a href="#categories/${category.id}" class="btn btn-sm btn-info text-white">Show</a>
                            <a href="#categories/${category.id}/edit" class="btn btn-sm btn-warning">Edit</a>
                            <button class="btn btn-sm btn-danger" onclick="window.deleteCategory(${category.id})">Delete</button>
                        </td>
                    </tr>
                `;
            });
            html += `</tbody></table>`;
            
            contentArea.innerHTML = html;
        } else {
            contentArea.innerHTML = `<div class="alert alert-danger">Error loading categories: ${result.message || 'API error.'}</div>`;
        }
    } catch (error) {
        console.error('Network Error:', error);
        contentArea.innerHTML = `<div class="alert alert-danger">Network Error: Cannot connect to the API. Check console for details.</div>`;
    }
}

// Category Detail View
async function categoryDetailView(id) {
    contentArea.innerHTML = `<h2>Category Detail (ID: ${id})</h2><p>Loading...</p>`;
    
    try {
        const response = await fetch(`${API_BASE_URL}/categories/${id}`);
        const result = await response.json();

        if (result.success && result.data) {
            const category = result.data;
            let html = `
                <div class="card p-4 shadow-lg">
                    <h3 class="card-title text-primary">Category Name: ${category.category_name}</h3>
                    <p><strong>ID:</strong> ${category.id}</p>
                    <div class="mt-3 d-flex gap-2">
                        <a href="#categories/${category.id}/edit" class="btn btn-warning"><i class="fas fa-edit me-1"></i> Edit Category</a>
                        <a href="#categories" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Back to List</a>
                    </div>
                </div>
            `;
            contentArea.innerHTML = html;
        } else {
            contentArea.innerHTML = `<div class="alert alert-danger">Error loading category: ${result.message || 'Category not found or API error.'}</div>`;
        }
    } catch (error) {
        console.error('Network Error:', error);
        contentArea.innerHTML = `<div class="alert alert-danger">Network Error: Could not load category details.</div>`;
    }
}

// Category Create View
function categoryCreateView() {
    contentArea.innerHTML = `
        <h1 class="mb-4">Create New Category</h1>
        <div id="createCategoryFormContainer" class="card p-4">Loading form...</div>
    `;

    fetch('frontend/views/categories/create.html')
        .then(response => {
            if (!response.ok) throw new Error('Failed to load form template');
            return response.text();
        })
        .then(html => {
            document.getElementById('createCategoryFormContainer').innerHTML = html;
            setupCreateCategoryView(); 
        })
        .catch(err => {
            console.error(err);
            document.getElementById('createCategoryFormContainer').innerHTML = `<div class="alert alert-danger">Error loading form assets.</div>`;
        });
}

// Category Edit View
async function categoryEditView(id) {
    contentArea.innerHTML = `<h1 class="mb-4">Edit Category (ID: ${id})</h1><div id="editCategoryFormContainer" class="card p-4">Loading category data...</div>`;
    const formContainer = document.getElementById('editCategoryFormContainer');

    try {
        const categoryResponse = await fetch(`${API_BASE_URL}/categories/${id}`);
        const categoryResult = await categoryResponse.json();

        if (!categoryResult.success) {
            formContainer.innerHTML = `<div class="alert alert-danger">Error fetching category data: ${categoryResult.message || 'Category not found.'}</div>`;
            return;
        }

        const categoryData = categoryResult.data;

        // Load the edit form template
        const templateResponse = await fetch('frontend/views/categories/edit.html');
        if (!templateResponse.ok) throw new Error('Failed to load edit form template');
        const html = await templateResponse.text();
        formContainer.innerHTML = html;

        // Populate form fields
        document.getElementById('categoryId').value = categoryData.id;
        document.getElementById('category_name').value = categoryData.category_name;
        
        // Setup form submission
        setupEditCategoryView(id);

    } catch (error) {
        console.error('Category Edit View Error:', error);
        formContainer.innerHTML = `<div class="alert alert-danger">Network or loading error: ${error.message}</div>`;
    }
}


// UPDATED: User Profile View (Fetches live data)
async function profileView() {
    contentArea.innerHTML = `<div class="text-center p-5"><i class="fas fa-spinner fa-spin fa-3x text-primary"></i><h4 class="mt-3">Loading Profile...</h4></div>`;

    try {
        // Fetch user profile data from the new backend endpoint (hardcoded to ID 1 on the backend)
        const response = await fetch(`${API_BASE_URL}/user/profile`);
        const result = await response.json();
        
        if (!result.success || !result.data) {
             contentArea.innerHTML = `<div class="alert alert-danger p-4">Error loading profile: ${result.message || 'User data could not be fetched.'}</div>`;
             return;
        }
        
        const user = result.data;
        
        // Display the data, including the dynamically fetched role_name
        contentArea.innerHTML = `
            <div class="card p-5 shadow-lg mx-auto" style="max-width: 600px;">
                <div class="text-center mb-4">
                    <i class="fas fa-user-circle fa-5x text-primary"></i>
                    <h1 class="mt-3">${user.username}</h1>
                </div>
                
                <div class="row border-top pt-3">
                    <div class="col-sm-6 mb-3">
                        <p class="mb-0 text-muted">User ID</p>
                        <p class="fw-bold">${user.id}</p>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <p class="mb-0 text-muted">Role ID</p>
                        <p class="fw-bold">${user.role_id}</p>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <p class="mb-0 text-muted">Email</p>
                        <p class="fw-bold">${user.email}</p>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <p class="mb-0 text-muted">Role Name</p>
                        <p class="fw-bold text-success">${user.role_name}</p>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <p class="mb-0 text-muted">Member Since</p>
                        <p class="fw-bold">${user.member_since}</p>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <p class="mb-0 text-muted">Clubs Managed</p>
                        <p class="fw-bold">${user.total_clubs_managed}</p>
                    </div>
                </div>
                
                <button class="btn btn-outline-primary mt-4 w-100"><i class="fas fa-cog me-2"></i> Edit Account Settings (Mock)</button>
            </div>
        `;
    } catch (error) {
        console.error('Profile View Network Error:', error);
        contentArea.innerHTML = `<div class="alert alert-danger p-4">Network Error: Cannot connect to the User API. Check console.</div>`;
    }
}


// --- 4. VIEW SETUP FUNCTIONS (Logic separated from rendering) ---

// Existing: Setup for Club Creation Form
function setupCreateClubView() {
    // 1. Function to Load Categories is now a separate function (loadCategories)
    
    // 2. Function to Handle Form Submission
    const form = document.getElementById('createClubForm');
    if (!form) return;
    
    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        const jsonData = {};
        for (const [key, value] of formData.entries()) {
            jsonData[key] = key === 'club_member_count' ? parseInt(value, 10) : value;
            jsonData['category_id'] = jsonData['category_id'] || 0;
        }
        if(jsonData.category_id === '') jsonData.category_id = 0;
        
        try {
            const response = await fetch(`${API_BASE_URL}/clubs`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(jsonData)
            });

            const result = await response.json();
            const alertDiv = document.getElementById('alertContainer');
            alertDiv.innerHTML = '';

            if (response.ok) {
                alertDiv.innerHTML = `<div class="alert alert-success">Club created! ID: ${result.id}</div>`;
                form.reset(); 
                
                setTimeout(() => {
                    window.location.hash = '#clubs';
                }, 1500);
            } else {
                alertDiv.innerHTML = `<div class="alert alert-danger">${result.message || 'An unknown server error occurred.'}</div>`;
            }

        } catch (error) {
            console.error('AJAX submission error:', error);
            document.getElementById('alertContainer').innerHTML = `<div class="alert alert-danger">Network error. Check console.</div>`;
        }
    });

    loadCategories(); 
}

// Setup for Club Edit Form
function setupEditClubView(id) {
    const form = document.getElementById('editClubForm');
    if (!form) return;
    
    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        const jsonData = {};
        for (const [key, value] of formData.entries()) {
            jsonData[key] = key === 'club_member_count' ? parseInt(value, 10) : value;
        }
        // Ensure category_id is set and properly cast
        jsonData['category_id'] = parseInt(jsonData['category_id'] || 0, 10);
        
        try {
            const response = await fetch(`${API_BASE_URL}/clubs/${id}`, {
                method: 'PUT',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(jsonData)
            });

            const result = await response.json();
            const alertDiv = document.getElementById('alertContainer');
            alertDiv.innerHTML = '';

            if (response.ok) {
                alertDiv.innerHTML = `<div class="alert alert-success">Club ID ${id} successfully updated!</div>`;
                
                setTimeout(() => {
                    window.location.hash = '#clubs';
                }, 1500);
            } else {
                alertDiv.innerHTML = `<div class="alert alert-danger">${result.message || 'An unknown server error occurred during update.'}</div>`;
            }

        } catch (error) {
            console.error('AJAX submission error:', error);
            document.getElementById('alertContainer').innerHTML = `<div class="alert alert-danger">Network error. Check console.</div>`;
        }
    });
}


// Existing: Setup for Category Creation Form
function setupCreateCategoryView() {
    const form = document.getElementById('createCategoryForm');
    if (!form) return;

    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        
        const formData = new FormData(this);
        const jsonData = {};
        for (const [key, value] of formData.entries()) {
            jsonData[key] = value;
        }

        try {
            const response = await fetch(`${API_BASE_URL}/categories`, {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(jsonData)
            });

            const result = await response.json();
            const alertDiv = document.getElementById('alertContainer');
            alertDiv.innerHTML = '';

            if (response.ok) { // Status 201
                alertDiv.innerHTML = `<div class="alert alert-success">Category created! ID: ${result.id}</div>`;
                form.reset(); 
                
                setTimeout(() => {
                    window.location.hash = '#categories'; // Redirect to category list
                }, 1500);
            } else { // Status 400 or 500
                alertDiv.innerHTML = `<div class="alert alert-danger">${result.message || 'An unknown server error occurred.'}</div>`;
            }

        } catch (error) {
            console.error('AJAX submission error:', error);
            document.getElementById('alertContainer').innerHTML = `<div class="alert alert-danger">Network error. Check console.</div>`;
        }
    });
}

// Setup for Category Edit Form
function setupEditCategoryView(id) {
    const form = document.getElementById('editCategoryForm');
    if (!form) return;
    
    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        const jsonData = {};
        for (const [key, value] of formData.entries()) {
            jsonData[key] = value;
        }
        
        try {
            const response = await fetch(`${API_BASE_URL}/categories/${id}`, {
                method: 'PUT',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(jsonData)
            });

            const result = await response.json();
            const alertDiv = document.getElementById('alertContainer');
            alertDiv.innerHTML = '';

            if (response.ok) {
                alertDiv.innerHTML = `<div class="alert alert-success">Category ID ${id} successfully updated!</div>`;
                
                setTimeout(() => {
                    window.location.hash = '#categories';
                }, 1500);
            } else {
                alertDiv.innerHTML = `<div class="alert alert-danger">${result.message || 'An unknown server error occurred during update.'}</div>`;
            }

        } catch (error) {
            console.error('AJAX submission error:', error);
            document.getElementById('alertContainer').innerHTML = `<div class="alert alert-danger">Network error. Check console.</div>`;
        }
    });
}


// Existing: Deletion functions are kept global
async function deleteClub(id) {
    if (!confirm(`Are you sure you want to delete club ID ${id}?`)) {
        return;
    }

    try {
        const response = await fetch(`${API_BASE_URL}/clubs/${id}`, {
            method: 'DELETE',
        });

        if (response.status === 204 || response.ok) {
            clubsListView(); 
        } else {
            const errorResult = await response.json();
            alert(`Deletion failed: ${errorResult.message || 'Server error.'}`);
        }
    } catch (error) {
        console.error('Deletion network error:', error);
        alert('Network error during deletion. Check console.');
    }
}

async function deleteCategory(id) {
    if (!confirm(`Are you sure you want to delete category ID ${id}? Clubs using this category will have their category_id set to NULL or 0.`)) {
        return;
    }

    try {
        const response = await fetch(`${API_BASE_URL}/categories/${id}`, {
            method: 'DELETE',
        });

        if (response.status === 204 || response.ok) {
            categoriesListView(); 
        } else {
            const errorResult = await response.json();
            alert(`Category deletion failed: ${errorResult.message || 'Server error.'}`);
        }
    } catch (error) {
        console.error('Category deletion network error:', error);
        alert('Network error during category deletion. Check console.');
    }
}

// Global exposure for event handlers
window.deleteClub = deleteClub;
window.deleteCategory = deleteCategory;