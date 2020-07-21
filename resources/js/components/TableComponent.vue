<template>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h1 class="float-left">Contacts</h1>
                    <button type="button" class="btn btn-primary float-right" data-toggle="modal" @click="setAction('add'), clearFields()" data-target="#contactModal">
                        Add Contact
                    </button>
                </div>
                <div class="card-body">
                    <input type="text" @keyup="searchContact()" v-model="search" class="form-control w-50 float-right mb-3" placeholder="Search" />
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">NAME</th>
                                <th scope="col">COMPANY</th>
                                <th scope="col">PHONE</th>
                                <th scope="col">EMAIL</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="contact in contacts" v-bind:key="contact.id">
                                <td>{{ contact.name }}</td>
                                <td>{{ contact.company }}</td>
                                <td>{{ contact.phone }}</td>
                                <td>{{ contact.email }}</td>
                                <td>
                                    <button type="button" @click="setAction('edit'), setData(contact)" data-toggle="modal" data-target="#contactModal" class="btn btn-primary btn-outline">Edit</button>
                                    <button type="button" @click="deleteContact(contact.id)" class="btn btn-danger">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-12 text-center">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="contactModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel"> {{ (this.action === 'add') ? 'Add' : 'Edit' }} contact</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" v-model="data.name" class="form-control" id="name" placeholder="Enter name">
                        </div>
                        <div class="form-group">
                            <label for="company">Company</label>
                            <input type="text" v-model="data.company"  class="form-control" id="company" placeholder="Enter company">
                        </div>
                        <div class="form-group">
                            <label for="company">Phone</label>
                            <input type="text" v-model="data.phone" class="form-control" id="phone" placeholder="Enter phone">
                        </div>
                        <div class="form-group">
                            <label for="email">Email address</label>
                            <input type="email" v-model="data.email"  class="form-control" id="email" placeholder="Enter email">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button v-if="this.action === 'add'" type="button" @click="saveContact()" class="btn btn-primary">Save</button>
                    <button v-if="this.action === 'edit'" type="button" @click="editContact()" class="btn btn-primary">Update</button>
                </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        created() {
            this.getAllContacts()
        },
        data: function() {
            return {
                action: '',
                search: '',
                contacts: [],
                current_page: 1,
                page: [],
                data: {
                    id: '',
                    name: '',
                    company: '',
                    phone: '',
                    email: '',
                    created_by: this.$userId
                }
            }
        },
        methods: {
            activeLink(id) {
                if (this.current_page === id) {
                    return true
                }
                return false
            },
            clearFields() {
                this.data.id = ''
                this.data.name = ''
                this.data.company = ''
                this.data.phone = ''
                this.data.email = ''
            },
            setAction(action) {
                this.action = action
            },
            setData(data) {
                this.data.id = data.id
                this.data.name = data.name
                this.data.company = data.company
                this.data.phone = data.phone
                this.data.email = data.email
            },
            getAllContacts() {
                let self = this
                axios.get('/api/contacts/user/' + this.$userId)
                    .then(function(result) {
                        self.page = result.data
                        let data = result.data.data
                        self.contacts = []
                        data.forEach(function(item) {
                            self.contacts.push(item)
                        }) 
                            
                    })
            },
            saveContact() {
                if (!this.data.name) {
                    toastr.error('Name is required!')
                    return;
                }

                let self = this

                axios.post('api/contacts', this.data)
                    .then(function(result) {
                        if (result.status === 201) {
                            self.getAllContacts()
                            $('#contactModal').modal('hide')
                            toastr.success('Successfully added.')
                        }
                    })
                    .catch((e) => {
                        console.log(e)
                    });
            },
            editContact() {

                let self = this

                axios.put('api/contacts/' + this.data.id, this.data)
                    .then(function(result) {
                        self.contacts.forEach(function(item, index) {
                            self.contacts = [];
                            self.getAllContacts();
                            $('#contactModal').modal('hide')
                        })
                    })
                    .catch((e) => {
                        console.log(e)
                    });
            },
            deleteContact(id) {

                let self = this

                if (confirm('Are you sure you want to DELETE?')) {
                    axios.delete('api/contacts/' + id)
                    .then(function(result) {
                        self.contacts = [];
                        self.getAllContacts();
                        toastr.success('Successfully deleted.')
                    })
                    .catch((e) => {
                        console.log(e)
                    });
                }
                        
            },
            searchContact() {

                let self = this

                if (this.search.length > 2) {
                    axios.get('api/contacts/search/' + this.search)
                    .then(function(result) {
                        if (result.data.length) {
                            self.contacts = []
                            if (result.data.length) {
                                result.data.forEach(function(item) {
                                    self.contacts.push(item)
                                })
                            }
                        }
                    })
                    .catch((e) => {
                        console.log(e)
                    });
                } else {
                    this.getAllContacts()
                }
            },
            goToPage (id) {
                this.getAllContacts();
            }
        }
    }
</script>

<style>
.active { color: #000; }
</style>
