<script>
import Layout from "../Layout.vue";
import Swal from "sweetalert2";
import axios from "axios";
import Loader from "../../loader/Loader.vue";

export default {
  components: {
    Layout,
    Loader,
  },

  data() {
    return {
      dataTable: null,
      purchased: [],
      loading: true,
    };
  },

  mounted() {
    this.getPurchased();
  },

  beforeRouteEnter(to, from, next) {
    next((vm) => {
      vm.initDataTable();
    });
  },
  beforeRouteLeave(to, from, next) {
    this.destroyDataTable();
    next();
  },

  methods: {
    formatDate(dateString) {
      // Create a new Date object from the dateString
      const date = new Date(dateString);

      // Define options for formatting
      const options = {
        day: "numeric",
        month: "long", // Use long month name
        year: "numeric",
      };

      // Format the date using options
      const formattedDate = date.toLocaleDateString("en-GB", options);

      return formattedDate;
    },

    getPurchased() {
      const token = this.$store.state.token;
      console.log(token);
      axios
        .get("https://appinventory.shuvobhowmik.xyz/api/purchased-received", {
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
        })
        .then((response) => {
          //console.log(response.data);
          this.loading = false;
          this.purchased = response.data;

          // After setting the data, initialize DataTables
          this.initDataTable();
        });
    },

    purchaseDelete(id) {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
      }).then((result) => {
        if (result.isConfirmed) {
          const token = this.$store.state.token;

          axios
            .get(`https://appinventory.shuvobhowmik.xyz/api/delete-purchase/${id}`, {
              headers: {
                "Content-Type": "application/json",
                Authorization: `Bearer ${token}`,
              },
            })
            .then((response) => {
              // Handle success response
              console.log(response.data);

              Swal.fire({
                toast: true,
                position: "top-right",
                animation: true,
                text: response.data.message,
                icon: "success",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
              });

              // Refresh the category list or update the UI accordingly
              this.getPurchased();
            })
            .catch((error) => {
              // Handle error response
              console.error(error);

              Swal.fire({
                toast: true,
                position: "top-right",
                animation: true,
                text: error.response.data.message,
                icon: "error",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
              });
            });
        }
      });
    },

    initDataTable() {
      // Destroy existing DataTable if it exists
      if (this.dataTable) {
        this.dataTable.destroy();
        this.dataTable = null;
      }

      // Initialize DataTable
      this.$nextTick(() => {
        this.dataTable = $("#example").DataTable();
      });
    },

    destroyDataTable() {
      if (this.dataTable) {
        this.dataTable.destroy();
        this.dataTable = null;
      }
    },
  },
};
</script>

<template>
  <div>
    <div
      v-if="loading"
      style="position: fixed; top: 50%; left: 50%; z-index: 1000"
    >
      <Loader />
    </div>

    <div>
      <Layout />

      <!--start page wrapper -->
      <div class="page-wrapper">
        <div class="page-content">
          <!--breadcrumb-->
          <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Dashboard</div>
            <div class="ps-3">
              <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                  <li class="breadcrumb-item">
                    <a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                  </li>
                  <li class="breadcrumb-item active" aria-current="page">
                    Purchase
                  </li>
                </ol>
              </nav>
            </div>
            <div class="ms-auto">
              <div class="btn-group">
               
              </div>
            </div>
          </div>
          <!--end breadcrumb-->
          <div style="display: flex; align-items: center; justify-content: space-between;">
            <h6 class="mb-0 text-uppercase">Manage Your Purchased</h6>

            <router-link to="/add-purchased">
                  <button
                    type="button"
                    class="btn btn-primary"
                    style="margin-left: 30px"
                  >
                    Add Purchased
                  </button>
                </router-link>
          </div>
          

          <hr />
          <div class="card">
            <div class="card-body">
              <div class="table-responsive">
                <table
                  id="example"
                  class="table table-striped table-bordered"
                  style="width: 100%"
                >
                  <thead>
                    <tr>
                      <th>Sl</th>
                      <th>Supplier Name</th>
                      <th>Product Name</th>
                      <th>Status</th>
                      <th>Quantity</th>

                      <th>Grand Total</th>
                      <th>Paid</th>
                      <th>Due</th>
                      <th>Date</th>

                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in this.purchased" :key="index">
                      <td>{{ index + 1 }}</td>
                      <td>{{ item.supplier.name }}</td>
                      <td>{{ item.product.name }}</td>

                      <td v-if="item.status === 'Ordered'">
                        <span
                          class="badge bg-success"
                          style="font-size: 13px"
                          >{{ item.status }}</span
                        >
                      </td>
                      <td v-else>
                        <span
                          class="badge bg-warning"
                          style="font-size: 13px"
                          >{{ item.status }}</span
                        >
                      </td>

                      <td>{{ item.qty }}</td>

                      <td>{{ item.g_total }}</td>
                      <td>{{ item.p_amount }}</td>
                      <td>{{ item.d_amount }}</td>

                      <td>{{ item.date }}</td>
                      <td
                        style="
                          display: flex;
                          align-items: center;
                          justify-content: flex-start;
                          gap: 10px;
                          padding-top: 20px;
                          padding-bottom: 20px;
                        "
                      >
                        <router-link
                          :to="'/purchase/edit/' + item.id"
                          type="button"
                          class="btn btn-primary px-5"
                        >
                          <i class="bx bx-pencil mr-1"></i>Edit
                        </router-link>
                        <button
                          type="button"
                          class="btn btn-danger px-5"
                          @click="purchaseDelete(item.id)"
                        >
                          <i class="bx bx-trash mr-1"></i>trash
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!--end page wrapper -->
    </div>
  </div>
</template>
