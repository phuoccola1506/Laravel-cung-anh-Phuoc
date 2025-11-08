let variantIndex = 0;

// Open modal to add new product
document.querySelector('[data-bs-target="#addProductModal"]').addEventListener('click', function () {
    document.getElementById('modalTitle').textContent = 'Thêm Sản Phẩm Mới';
    document.getElementById('productForm').reset();
    document.getElementById('productId').value = '';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('variantsContainer').innerHTML = '';
    
    // Reset image preview
    document.getElementById('imagePreview').style.display = 'none';
    document.getElementById('previewImg').src = '';
    
    variantIndex = 0;
    addVariantRow(); // Add one default variant row
});

// Add variant row
function addVariantRow(variant = null) {
    const container = document.getElementById('variantsContainer');
    const index = variantIndex++;

    // Parse attributes if it's a string
    let attributes = {};
    if (variant?.attributes) {
        if (typeof variant.attributes === 'string') {
            try {
                attributes = JSON.parse(variant.attributes);
            } catch (e) {
                console.error('Error parsing attributes:', e);
                attributes = {};
            }
        } else {
            attributes = variant.attributes;
        }
    }

    // Create inputs for all attributes dynamically
    let attributesHtml = '';
    const commonAttributes = ['color', 'color_code', 'storage', 'ram', 'battery', 'screen_size', 'weight'];
    
    // First, render common attributes
    commonAttributes.forEach(key => {
        const value = attributes[key] || '';
        const label = {
            'color': 'Màu sắc',
            'color_code': 'Mã màu',
            'storage': 'Bộ nhớ',
            'ram': 'RAM',
            'battery': 'Pin',
            'screen_size': 'Kích thước màn hình',
            'weight': 'Trọng lượng'
        }[key] || key;
        
        attributesHtml += `
            <div class="col-md-4">
                <label class="form-label">${label}</label>
                <input type="text" class="form-control" 
                       name="variants[${index}][attributes][${key}]" 
                       value="${value}" 
                       placeholder="${label}...">
            </div>
        `;
    });

    // Then, render any additional attributes not in common list
    Object.keys(attributes).forEach(key => {
        if (!commonAttributes.includes(key)) {
            attributesHtml += `
                <div class="col-md-4">
                    <label class="form-label">${key}</label>
                    <input type="text" class="form-control" 
                           name="variants[${index}][attributes][${key}]" 
                           value="${attributes[key]}" 
                           placeholder="${key}...">
                </div>
            `;
        }
    });

    const row = document.createElement('div');
    row.className = 'card mb-3 variant-row';
    row.setAttribute('data-variant-id', variant?.id || '');
    row.setAttribute('data-variant-index', index);
    row.innerHTML = `
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Biến thể #${index + 1}</h6>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeVariantRow(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <input type="hidden" name="variants[${index}][id]" value="${variant?.id || ''}">
                    
                    <div class="mb-3">
                        <h6 class="text-muted small mb-2"><i class="fas fa-info-circle"></i> Thông tin cơ bản</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">SKU</label>
                                <input type="text" class="form-control" name="variants[${index}][sku]" 
                                       value="${variant?.sku || ''}" placeholder="SKU-001">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Giá (đ) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="variants[${index}][price]" 
                                       value="${variant?.price || ''}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Giảm giá (%)</label>
                                <input type="number" class="form-control" name="variants[${index}][discount]" 
                                       value="${variant?.discount || 0}" min="0" max="100">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Tồn kho</label>
                                <input type="number" class="form-control" name="variants[${index}][stock]" 
                                       value="${variant?.stock || 0}" min="0">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Hình ảnh biến thể (tùy chọn)</label>
                        <input type="file" class="form-control" name="variants[${index}][image]" 
                               accept="image/*" onchange="previewVariantImage(event, ${index})">
                        <small class="text-muted">Nếu không upload, sẽ dùng ảnh sản phẩm chính</small>
                        <div id="variantImagePreview${index}" class="mt-2" style="${variant?.image ? 'display: block;' : 'display: none;'}">
                            <img id="variantPreviewImg${index}" 
                                 src="${variant?.image ? '/storage/' + variant.image : ''}" 
                                 alt="Preview" 
                                 style="max-width: 150px; max-height: 150px; border-radius: 8px; border: 1px solid #ddd;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="text-muted small mb-0"><i class="fas fa-cog"></i> Thuộc tính (Attributes)</h6>
                            <button type="button" class="btn btn-sm btn-success" onclick="addAttributeField(${index})">
                                <i class="fas fa-plus me-1"></i> Thêm thuộc tính
                            </button>
                        </div>
                        <div class="row g-3" id="attributesContainer${index}">
                            ${attributesHtml}
                        </div>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" 
                               name="variants[${index}][active]" 
                               ${!variant || variant.active == 1 ? 'checked' : ''}>
                        <label class="form-check-label">Đang bán</label>
                    </div>
                </div>
            `;

    container.appendChild(row);
}

// Add attribute field dynamically
function addAttributeField(variantIndex) {
    const attrName = prompt('Nhập tên thuộc tính (VD: color, storage, ram, battery, connection, sensor, dpi...):');
    
    if (!attrName || attrName.trim() === '') {
        return;
    }

    const cleanAttrName = attrName.trim().toLowerCase().replace(/\s+/g, '_');
    const container = document.getElementById(`attributesContainer${variantIndex}`);

    // Check if attribute already exists
    const existingInputs = container.querySelectorAll(`input[name="variants[${variantIndex}][attributes][${cleanAttrName}]"]`);
    if (existingInputs.length > 0) {
        alert('Thuộc tính này đã tồn tại!');
        return;
    }

    const labelMap = {
        'color': 'Màu sắc',
        'color_code': 'Mã màu',
        'storage': 'Bộ nhớ',
        'ram': 'RAM',
        'battery': 'Pin',
        'screen_size': 'Kích thước màn hình',
        'weight': 'Trọng lượng',
        'camera': 'Camera',
        'display': 'Màn hình',
        'cpu': 'CPU',
        'gpu': 'GPU',
        'os': 'Hệ điều hành',
        'connection': 'Kết nối',
        'sensor': 'Cảm biến',
        'dpi': 'DPI'
    };

    const label = labelMap[cleanAttrName] || cleanAttrName.charAt(0).toUpperCase() + cleanAttrName.slice(1);

    const newField = document.createElement('div');
    newField.className = 'col-md-4 attribute-field';
    newField.innerHTML = `
        <label class="form-label">${label} <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeAttributeField(this)" title="Xóa thuộc tính"><i class="fas fa-times"></i></button></label>
        <input type="text" class="form-control" 
               name="variants[${variantIndex}][attributes][${cleanAttrName}]" 
               placeholder="${label}...">
    `;

    container.appendChild(newField);
}

// Remove attribute field
function removeAttributeField(btn) {
    if (!confirm('Bạn có chắc muốn xóa thuộc tính này?')) {
        return;
    }
    btn.closest('.attribute-field').remove();
}

// Remove variant row
function removeVariantRow(btn) {
    if (document.querySelectorAll('.variant-row').length <= 1) {
        alert('Phải có ít nhất một biến thể!');
        return;
    }
    btn.closest('.variant-row').remove();
}

// Edit product
function editProduct(id) {
    fetch(`/admin/products/${id}/edit`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const product = data.product;

                document.getElementById('modalTitle').textContent = 'Chỉnh Sửa Sản Phẩm';
                document.getElementById('productId').value = product.id;
                document.getElementById('formMethod').value = 'PUT';
                document.getElementById('productName').value = product.name;
                document.getElementById('productCategory').value = product.category_id;
                document.getElementById('productBrand').value = product.brand_id;
                document.getElementById('productDescription').value = product.description || '';
                
                // Show existing image preview
                const imagePreview = document.getElementById('imagePreview');
                const previewImg = document.getElementById('previewImg');
                if (product.image) {
                    previewImg.src = '/images/' + product.image;
                    imagePreview.style.display = 'block';
                } else {
                    imagePreview.style.display = 'none';
                }
                
                document.getElementById('productStatus').checked = product.active == 1;

                // Clear and load variants
                document.getElementById('variantsContainer').innerHTML = '';
                variantIndex = 0;

                if (product.variants && product.variants.length > 0) {
                    product.variants.forEach(variant => {
                        addVariantRow(variant);
                    });
                } else {
                    addVariantRow(); // Add one default if no variants
                }

                const modal = new bootstrap.Modal(document.getElementById('addProductModal'));
                modal.show();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi tải dữ liệu sản phẩm!');
        });
}

// Save product (Create or Update)
function saveProduct() {
    const form = document.getElementById('productForm');
    const formData = new FormData(form);
    const productId = document.getElementById('productId').value;

    let url = '/admin/products';
    if (productId) {
        url = `/admin/products/${productId}`;
    }

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                bootstrap.Modal.getInstance(document.getElementById('addProductModal')).hide();
                window.location.reload();
            } else {
                let errorMsg = 'Có lỗi xảy ra!\n';
                if (data.errors) {
                    Object.values(data.errors).forEach(error => {
                        errorMsg += error[0] + '\n';
                    });
                }
                alert(errorMsg);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa sản phẩm!');
        });
}

// Preview image before upload
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (file) {
        // Validate file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('Kích thước file không được vượt quá 2MB!');
            event.target.value = '';
            preview.style.display = 'none';
            return;
        }
        
        // Validate file type
        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            alert('Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP)!');
            event.target.value = '';
            preview.style.display = 'none';
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
}

// Preview variant image before upload
function previewVariantImage(event, variantIndex) {
    const file = event.target.files[0];
    const preview = document.getElementById(`variantImagePreview${variantIndex}`);
    const previewImg = document.getElementById(`variantPreviewImg${variantIndex}`);
    
    if (file) {
        // Validate file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('Kích thước file không được vượt quá 2MB!');
            event.target.value = '';
            preview.style.display = 'none';
            return;
        }
        
        // Validate file type
        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            alert('Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP)!');
            event.target.value = '';
            preview.style.display = 'none';
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
}

// Delete product
function deleteProduct(id) {
    if (!confirm('Bạn có chắc muốn xóa sản phẩm này? Tất cả biến thể cũng sẽ bị xóa!')) {
        return;
    }

    fetch(`/admin/products/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Có lỗi xảy ra khi xóa sản phẩm!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa sản phẩm!');
        });
}