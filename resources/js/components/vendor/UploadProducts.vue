<template>
    <div class="container-fluid pending-vend">
        <div class="row" style="margin-top: 20px;">
            <div class="col-sm-12">
                <div class="top-newOrder myorder mb-4">
                    <div class="row mb-4 up-main-bx">
                        <div class="col-sm-6">
                            <div class="up-main-bx-1">
                                <div class="form-outline-ft mb-5">
                                    <input v-model="product.name" type="text" placeholder="Apple Airpod" class="form-control-label" required>
                                    <input id="vendor_id" v-model="product.vendor_id" type="hidden">
                                    <label class="form-label">Product Name</label>
                                    <p class="input-tip-bx">Do not exceed 20 characters when entering the
                                        product name.</p>
                                </div>
                                <div class="form-outline-ft mb-5">
                                    <input v-model="product.net_price" type="number" placeholder="200" class="form-control-label" required>
                                    <label class="form-label">Price</label>
                                </div>
                                <div class="form-outline-ft mb-5">
                                    <img src="/img/help-icon.png" class="help-tag-righ" width="30" height="30">
                                    <select v-model="product.parent_category" @change="updateSubCategories()" class="parentCategory form-control-label select-custom-point">
                                        <option v-for="(category, index) in this.catlist"
                                                :value="category.id">{{ category.title }}</option>
                                    </select>
                                    <span class="form-label">Category</span>
                                </div>
                                <div class="form-outline-ft mb-5">
                                    <img src="/img/help-icon.png" class="help-tag-righ" width="30" height="30">
                                    <select v-model="product.sub_category" class="subCategory form-control-label select-custom-point">
                                        <option v-for="(category, index) in this.subcatlist"
                                                :value="category.id">{{ category.title }}</option>
                                    </select>
                                    <span class="form-label">Sub-Category</span>
                                </div>
                                <div class="form-outline-ft mb-5">
                                    <img src="/img/help-icon.png" class="help-tag-righ" width="30" height="30">
                                    <select v-model="product.brand" class="form-control-label select-custom-point">
                                        <option v-for="brand in brands"
                                        :value="brand">{{ brand }}</option>
                                    </select>
                                    <span class="form-label">Brand</span>
                                </div>
                                <div class="form-outline-ft mb-5">
                                    <img src="/img/help-icon.png" class="help-tag-righ" width="30" height="30">
                                    <textarea v-model="product.description" class="form-control" rows="6"></textarea>

                                    <p class="input-tip-bx">Do not exceed 100 characters when entering the product description.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="up-main-bx-2">
                                <div class="form-outline-ft mb-5">
                                    <select v-model="product.status" class="form-control-label select-custom-point">
                                        <option v-for="status in product_status"
                                                :value="status">{{ status }}</option>
                                    </select>
                                    <span class="form-label">Status</span>
                                </div>

                                <div class="uploadimage-vup">
                                    <div class="upload-title-vup">
                                        <p>Product Image <img src="/img/help-icon.png" class="img-ut-vup"
                                                              width="30" height="30"></p>
                                    </div>
                                    <div class="upload-image-vup">
                                        <img id="img-upload-vup" src="/img/img-upload-dummy.jpg" class="img-fluid img-upload-vup">
                                        <input type="file" ref="file" style="display: none" name="featured_image" @change="featuredImage" />
                                        <button class="img-title-up" @click="$refs.file.click()">Upload Image</button>
                                    </div>
                                </div>

                                <div class="uploadimage-vup">
                                    <div class="upload-title-vup">
                                        <p>Product Images Gallery</p>
                                    </div>
                                    <div class="upload-image-vup">
                                        <img id="img-upload-vup" src="/img/img-upload-dummy.jpg" class="img-fluid img-upload-vup">
                                        <input type="file" multiple ref="file2" style="display: none" name="images" @change="galleryImage" />
                                        <button class="img-title-up" @click="$refs.file2.click()">Upload Images</button>
                                    </div>
                                </div>
                                <div>
                                    <button @click="addProduct" class="primary">
                                        {{ processing ? "PLEASE WAIT..." : "SAVE PRODUCT" }}
                                    </button>

                                    <span v-for="(error, index) in errors" class="validationError">
                                        {{ error[0] }}
                                    </span>

                                    <input type="file" ref="csv" style="display: none" name="csv_upload" @change="csvUpload" />
                                    <button class="primary" @click="$refs.csv.click()">
                                        UPLOAD MULTIPLE PRODUCTS
                                    </button>
                                    <button class="primary">REMOVE PRODUCT</button>
                                    <a class="downl-csv" target="_blank" download href="/files/products-sample.csv">DOWNLOAD SAMPLE TEMPLATE</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    data() {
        return {


            product_id:             null,
            csv_file:               null,
            parent_categories:      null,
            sub_categories:         null,
            processing:             false,
            errors:                 null,
            user:                   this.$store.state.auth.user,
            brands:                 null,
            product: {
                id:                     '',
                name:                   '',
                net_price:              '',
                vendor_id:              '',
                parent_category:        '',
                sub_category:        '',
                brand:                  '',
                description:            '',
                featured_image:         '',
                imagesArray: [],
                status:                 '',
                _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            catlist:[],
            subcatlist:[]
        }
    },

    created() {
        this.parent_categories          = SITE_CATEGORIES;
        this.brands                     = SITE_BRANDS;
        this.product_status             = PRODUCT_STATUS;
        this.product.vendor_id          = this.user.id;
        this.product_id                 = this.$route.params.product_id;

        if(this.product_id) {
            this.getProductById(this.product_id);
        }
    },
    mounted(){
        this.loadCategories();
    },

    methods: {

        async loadCategories(){
            document.getElementById('ajaxLoader').style.display = 'block';
            let result = axios.get("/categories");
            console.warn("Check Data");
            const obj = (await result).data;
            console.warn(obj);
            if(obj.success==true){
              this.catlist = obj.data;
            } else {
              alert("Issue loading categories");
            }
            document.getElementById('ajaxLoader').style.display = 'none';
        },
        async loadSubCategories(id){
//alert(id)
            document.getElementById('ajaxLoader').style.display = 'block';
            let result = axios.get("/categories", 
                    {
                      params: {
                        id: id
                      },
                    },
                    { useCredentails: true }
            );
            console.warn("Check Data");
            const obj = (await result).data;
            console.warn(obj);
            if(obj.success==true){
              this.subcatlist = obj.data;
            } else {
              alert("Issue loading categories");
            }
            document.getElementById('ajaxLoader').style.display = 'none';
        },




        updateSubCategories() {
            var parent = document.getElementsByClassName('parentCategory');

            if (typeof parent[0] !== 'undefined') {
                parent = parent[0].value;
                if(parent.length < 1) {
                    document.getElementsByClassName('subCategory')[0].value = ""
                }
                //this.sub_categories = SITE_CATEGORIES[parent];
                this.loadSubCategories(parent)
            }
        },
        getProductById(product) {
            document.getElementById('ajaxLoader').style.display = 'block';
            let url = '/api/product/get/' + product;
            fetch(url)
                .then(res => res.json())
                .then(res => {
                    this.product.parent_category        = res.parent_category;
                    document.getElementsByClassName('parentCategory')[0].value = res.parent_category;
                    this.updateSubCategories();
                    if(res.featured_image) {
                        let src = "/img/product-images/" + res.vendor_id + "/" + res.featured_image;
                        document.getElementById('img-upload-vup').src = src;
                    }
                    this.product.id                     = res.id;
                    this.product.name                   = res.name;
                    this.product.net_price              = res.net_price;
                    this.product.vendor_id              = res.vendor_id;
                    this.product.status                 = res.status;
                    this.product.sub_category           = res.sub_category;
                    this.product.brand                  = res.brand;
                    this.product.description            = res.description;
                    console.log(this.product);
                })
                .catch(err => console.log(err))
                .finally(() => {
                    document.getElementById('ajaxLoader').style.display = 'none';
                });
        },
        csvUpload(e) {
            document.getElementById('ajaxLoader').style.display = 'block';

            this.csv_file = e.target.files[0];
            this.processing = true;

            var object = this;

            const config = {
                headers: {
                    'content-type': 'multipart/form-data'
                }
            }

            let data = new FormData();
            data.append('csv_file', this.csv_file);
            data.append('vendor_id', this.product.vendor_id);

            axios.post('/product-csv', data, config)
                .then(function (res) {
                    var data = res.data;
                    if (data.success == 'true') {
                        alert('The CSV was imported successfully.');
                        object.clearForm();
                    } else {
                        object.errors = data.errors;
                    }
                })
                .catch(function (res) {
                    console.log('--');
                    console.log(res);
                    console.log('--');
                })
                .finally(()=>{
                    this.processing = false;
                    document.getElementById('ajaxLoader').style.display = 'none';
                });
        },
        featuredImage(e) {
            this.product.featured_image = e.target.files[0];
        },
        galleryImage(e) {
            this.product.imagesArray = e.target.files;
        },
        addProduct() {
            document.getElementById('ajaxLoader').style.display = 'block';
            this.processing = true;

            var object = this;

            const config = {
                headers: {
                    'content-type': 'multipart/form-data'
                }
            }

            let data = new FormData();
            data.append('id', this.product.id);
            data.append('name', this.product.name);
            data.append('net_price', this.product.net_price);
            data.append('vendor_id', this.product.vendor_id);
            data.append('parent_category', this.product.parent_category);
            data.append('description', this.product.description);
            data.append('brand', this.product.brand);
            data.append('featured_image', this.product.featured_image);
            data.append('images', this.product.imagesArray.length);

            for (const i of Object.keys(this.product.imagesArray)) {
                data.append('imagesArray_'+i, this.product.imagesArray[i])
            }
            data.append('status', this.product.status);
            data.append('sub_category', this.product.sub_category);

            axios.post('/product', data, config)
                .then(function (res) {
                    var data = res.data;
                    if (data.success == 'true') {
                        alert('product created successfully.');
                        object.clearForm();
                    } else {
                        object.errors = data.errors;
                    }
                })
                .catch(function (res) {
                    console.log(res);
                })
                .finally(()=>{
                    this.processing = false;
                    document.getElementById('ajaxLoader').style.display = 'none';
                });
        },
        clearForm() {
            this.product.name = null;
            this.product.status = null;
            this.product.sub_category = null;
            this.product.net_price = null;
            this.product.parent_category = null;
            this.product.brand = null;
            this.product.description = null;
        }
    }
}
</script>
