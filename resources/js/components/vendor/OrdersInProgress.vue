<template>
    <div class="container-fluid pending-vend">
        <div class="row" style="margin-top: 20px;">
            <div class="col-sm-12">
                <div class="top-newOrder mb-4">
                
                    
                    <div class="row">
                            <div class="col-lg-8 col-12">
                                <div class="search-box mb-4">
                                    <img src="/img/search-icon.png" class="search-icon" alt="">
                                    <input type="text" class="search_BX"
                                    v-on:keypress="searchObjects">
                                    <img src="/img/close-srch.png" @click="removeSearch" class="close-icon" alt="">
                                    </div>
                            </div>
                            <div class="col-lg-4 col-12">
                                <DateFilter :year.sync="year" :month.sync="month" @fetch="fetch()"></DateFilter>                        
                            </div>
                            <!-- end filter -->
                            <div v-if="single_order != 0" class="col-lg-5 col-12">
                                <div class="inpro-order-detail">
                                    <table class="tablebg-inpro">
                                        <tbody>
                                        <tr>
                                            <td>Order Id:</td><td>{{ single_order.id }}</td>
                                        </tr>
                                        <tr>
                                            <td>Order Date:</td><td>{{ single_order.ordered_at }}</td>
                                        </tr>
                                        <tr>
                                            <td>Ship Date:</td><td>{{ single_order.shipping_date }}</td>
                                        </tr>
                                        <tr>
                                            <td>Shipping Address:</td><td>{{ single_order.shipping_address }}</td>
                                        </tr>
                                        <tr>
                                            <td>Carrier:</td><td>{{ single_order.shipping_method }}</td>
                                        </tr>
                                        <tr>
                                            <td>Carrier Tracking Number:</td><td>US Postal Service</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div v-if="single_order != 0" class="order-progress-ar mob-ordInProg">
                                    <div class="opar-bx">
                                        <!-- start:: -->
                                        <div class="stepper-wrapper">
                                            <div id="received" class="stepper-item">
                                                <div class="step-counter"></div>
                                                <div class="step-name">Received</div>
                                            </div>
                                            <div id="at_depot" class="stepper-item">
                                                <div class="step-counter"></div>
                                                <div class="step-name">At Depot</div>
                                            </div>
                                            <div id="in_transit" class="stepper-item">
                                                <div class="step-counter"></div>
                                                <div class="step-name">In Transit</div>
                                            </div>
                                            <div id="out_for_delivery" class="stepper-item">
                                                <div class="step-counter"></div>
                                                <div class="step-name">Out For Delivery</div>
                                            </div>
                                            <div id="delivered" class="stepper-item">
                                                <div class="step-counter"></div>
                                                <div class="step-name">Delivered</div>
                                            </div>
                                        </div>
                                        <!-- end :::  -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                        <div class="col-sm-12">
                            <!-- START: FIRST TAB CONTENT -->
                            <div id="first-pvs-posh">
                                <h1 class=" border-b-txt">In-Progress Orders</h1>
                                <!-- start: TABLE -->
                                <table class="table  recent-Orders-table mobile-btn-show" id="pvs-tab">
                                    <thead>
                                    <tr>
                                        <th @click="fetch(0, 0, 'id')" class="shuffle-bx" scope="col" ><span class="sort-ad">Order Id <img class="shuffle" src="/img/shuffle.png"></span>
                                        </th>
                                        <th @click="fetch(0, 0, 'first_name')" class="shuffle-bx" scope="col"><span class="sort-ad">Recipient <img class="shuffle" src="/img/shuffle.png"></span>
                                        </th>
                                        <th @click="fetch(0, 0, 'ordered_at')" class="shuffle-bx" scope="col"><span class="sort-ad">Date <img class="shuffle" src="/img/shuffle.png"></span>
                                        </th>
                                        <th scope="col">Shipping Details</th>
                                        <th scope="col">Delivery Status</th>
                                        <th scope="col">Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="order in orders">
                                            <td>
                                                <span>{{ order.id }}</span>
                                                <i class="fa fa-angle-double-down mob-expand" aria-hidden="true"></i>
                                            </td>
                                            <td>
                                                <span>{{ order.first_name }} {{ order.last_name }}</span>
                                            </td>
                                            <td>
                                                <span>{{ order.ordered_at }}</span>
                                            </td>
                                            <td>
                                                <span>{{ order.shipping_address }}</span>
                                            </td>
                                            <td>
                                                <span class="pendig-txt">{{ order.progress }}</span>
                                            </td>
                                            <td>
                                                <span class="track-btn" @click="getVendorOrderById(order.id)">Track</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="foot-table" v-if="total < 1">
                                    <p>No records found.</p>
                                </div>
                                <div class="foot-table" v-if="total > 0">
                                    <div class="left"><span>Rows Per Page:
                                            <select
                                                @change="fetch(current_page, $event.target.value)">
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="75">75</option>
                                                <option value="100">100</option>
                                            </select>
                                            </span></div>
                                    <div class="right">
                                        <span>{{ from }}-{{ to }} of {{ total }} Items</span>
                                        <img src="/img/prev-arrow.png"  @click="fetch(current_page-1)" alt="" class="prev-itm">
                                        <img src="/img/next-arrow.png" @click="fetch(current_page+1)" alt="" class="next-itm"></div>
                                    <!-- END: TABLE   -->
                                </div>
                                <!-- END::: FIRST TAB CONTENT -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import DateFilter from '../DateFilter'

export default {
    components: {
        DateFilter
    },
    data() {
        return {
            user: this.$store.state.auth.user,
            per_page: 25,
            date_range:     null,
            year:           '',
            month:          '',
            order_by: 'id',
            order: 'desc',
            search: 0,
            to: null,
            from: null,
            total: null,
            current_page: null,
            orders: [],
            single_order: 0,
        }
    },
    created() {
        this.fetch();
    },
    methods: {
        getVendorOrderById(id) {
            const childrens = document.querySelectorAll('.stepper-item');
            childrens.forEach(function(e) {
                e.classList.remove('active');
            });
            document.getElementById('ajaxLoader').style.display = 'block';
            var url = '/api/vendor-order/'+ id;

            fetch(url)
                .then(res => res.json())
                .then(res => {
                    this.single_order = res;
                    let progress = res.progress;
                    progress = progress.replace(/ /g, "_");

                    setTimeout(function () {
                        document.getElementById(progress).classList.add('active');
                    }, 1000);
                })
                .catch(err => console.log(err))
                .finally(function () {
                    document.getElementById('ajaxLoader').style.display = 'none';
                });
        },
        fetch(page = 0, per_page = 0, order_by = 0, search = 0) {
            document.getElementById('ajaxLoader').style.display = 'block';
            var url = '/api/orders/vendor/'+ this.user.id;

            if (per_page > 0 || this.per_page > 0) {
                if (per_page > 0) {
                    this.per_page = per_page;
                }
                url += '/' + this.per_page;
            } else {
                url += '/25';
            }

            if (order_by != 0 || this.order_by > 0) {
                if (order_by != 0) {
                    this.order_by = order_by;
                    if (this.order == 'asc') {
                        this.order = 'desc';
                    } else {
                        this.order = 'asc';
                    }
                }
                url += '/' + this.order_by;
                url += '/' + this.order;
            } else {
                url += '/id';
                url += '/desc';
            }

            var search = search;

            if (search != 0 || this.search != 0) {
                if (search != 0) {
                    this.search = search;
                }
                url += '/' + this.search;
            } else {
                url += '/0';
            }

            url += '/approved';

            this.date_range = '';

            if (this.year && this.year != 'null') {
                this.date_range = this.year;
            }

            if (this.month && this.month != 'null') {
                this.date_range += '-' + this.month + '-';
            }

            if (this.date_range) {
                url += '/' + this.date_range;
            }

            if (page > 0) {
                url += '?page=' + page;
            }

            fetch(url)
                .then(res => res.json())
                .then(res => {
                    this.orders = res.data;
                    this.to = res.to;
                    this.from = res.from;
                    this.total = res.total;
                    if (res.total < res.per_page) {
                        this.from = 0;
                    }
                    this.current_page = res.to / res.per_page;
                })
                .catch(err => console.log(err))
                .finally(function () {
                    document.getElementById('ajaxLoader').style.display = 'none';
                });
        },

        searchObjects: function (e) {
            if (e.keyCode === 13) {
                var element = e.target;
                this.fetch(0, 0, 0, element.value);
            }
        },

        removeSearch: function () {
            document.getElementsByClassName('search_BX')[0].value = '';
            this.search = 0;
            this.fetch();
        },
    }
}
</script>
