<template>
    <div class="card">
        <div class="card-body">
            <el-steps :active="active" finish-status="success">
                <el-step title="Empresa">
                    <template #icon>
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler-outline icon-tabler-building">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M3 21h18" />
                            <path d="M9 8h1" />
                            <path d="M9 12h1" />
                            <path d="M9 16h1" />
                            <path d="M14 8h1" />
                            <path d="M14 12h1" />
                            <path d="M14 16h1" />
                            <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16" />
                        </svg>
                    </template>
                </el-step>
            
                <el-step title="Certificado">
                    <template #icon>
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-file-description">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                            <path d="M9 17h6" />
                            <path d="M9 13h6" />
                        </svg>
                    </template>
                </el-step>
            </el-steps>
            <br />
            <br />

            <!-- Empresa -->
            <div class="row" v-show="active == 0">
                <div class="col-md-12">
                    <form autocomplete="off">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group"
                                        :class="{'has-danger': errors.type_document_identification_id}">
                                        <label class="control-label">Tipo de Documento</label>
                                        <el-select class="extend" v-model="form.type_document_identification_id"
                                            filterable>
                                            <el-option v-for="option in type_document_identification"
                                                :key="option.id" :value="option.id" :label="option.name">
                                            </el-option>
                                        </el-select>
                                        <small class="form-control-feedback"
                                            v-if="errors.type_document_identification_id"
                                            v-text="errors.type_document_identification_id[0]"></small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group" :class="{'has-danger': errors.nit}">
                                        <label class="control-label">Número de documento</label>
                                        <el-input v-model="form.nit"></el-input>
                                        <small class="form-control-feedback" v-if="errors.nit"
                                            v-text="errors.nit[0]"></small>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group" :class="{'has-danger': errors.dv}">
                                        <label class="control-label">DV</label>
                                        <el-input v-model="form.dv"></el-input>
                                        <small class="form-control-feedback" v-if="errors.dv"
                                            v-text="errors.dv[0]"></small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" :class="{'has-danger': errors.business_name}">
                                        <label class="control-label">Empresa</label>
                                        <el-input v-model="form.business_name"></el-input>
                                        <small class="form-control-feedback" v-if="errors.business_name"
                                            v-text="errors.business_name[0]"></small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" :class="{'has-danger': errors.merchant_registration}">
                                        <label class="control-label">Registro Mercantil</label>
                                        <el-input v-model="form.merchant_registration"></el-input>
                                        <small class="form-control-feedback" v-if="errors.merchant_registration"
                                            v-text="errors.merchant_registration[0]"></small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" :class="{'has-danger': errors.phone}">
                                        <label class="control-label">Telefono</label>
                                        <el-input v-model="form.phone"></el-input>
                                        <small class="form-control-feedback" v-if="errors.phone"
                                            v-text="errors.phone[0]"></small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" :class="{'has-danger': errors.email}">
                                        <label class="control-label">Correo Electronico</label>
                                        <el-input v-model="form.email"></el-input>
                                        <small class="form-control-feedback" v-if="errors.email"
                                            v-text="errors.email[0]"></small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" :class="{'has-danger': errors.department_id}">
                                        <label class="control-label">Departamento</label>
                                        <el-select class="extend" @change="filterMunicipality"
                                            v-model="form.department_id" filterable>
                                            <el-option v-for="option in department" :key="option.id"
                                                :value="option.id" :label="option.name"></el-option>
                                        </el-select>
                                        <small class="form-control-feedback" v-if="errors.department_id"
                                            v-text="errors.department_id[0]"></small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" :class="{'has-danger': errors.municipality_id}">
                                        <label class="control-label">Municipio</label>
                                        <el-select class="extend" v-model="form.municipality_id" filterable>
                                            <el-option v-for="option in municipality_filter" :key="option.id"
                                                :value="option.id" :label="option.name"></el-option>
                                        </el-select>
                                        <small class="form-control-feedback" v-if="errors.municipality_id"
                                            v-text="errors.municipality_id[0]"></small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" :class="{'has-danger': errors.address}">
                                        <label class="control-label">Direccion</label>
                                        <el-input v-model="form.address"></el-input>
                                        <small class="form-control-feedback" v-if="errors.address"
                                            v-text="errors.address[0]"></small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group"
                                        :class="{'has-danger': errors.type_liability_id}">
                                        <label class="control-label">Tipo Responsabilidad</label>
                                        <el-select class="extend" v-model="form.type_liability_id"
                                            filterable>
                                            <el-option v-for="option in type_liability"
                                                :key="option.id" :value="option.id" :label="option.name">
                                            </el-option>
                                        </el-select>
                                        <small class="form-control-feedback"
                                            v-if="errors.type_liability_id"
                                            v-text="errors.type_liability_id[0]"></small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" :class="{'has-danger': errors.type_organization_id}">
                                        <label class="control-label">Organizacion</label>
                                        <el-select class="extend" v-model="form.type_organization_id" filterable>
                                            <el-option v-for="option in type_organization" :key="option.id"
                                                :value="option.id" :label="option.name"></el-option>
                                        </el-select>
                                        <small class="form-control-feedback" v-if="errors.type_organization_id"
                                            v-text="errors.type_organization_id[0]"></small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group" :class="{'has-danger': errors.type_regime_id}">
                                        <label class="control-label">Regimen</label>
                                        <el-select class="extend" v-model="form.type_regime_id" filterable>
                                            <el-option v-for="option in type_regime" :key="option.id"
                                                :value="option.id" :label="option.name"></el-option>
                                        </el-select>
                                        <small class="form-control-feedback" v-if="errors.type_regime_id"
                                            v-text="errors.type_regime_id[0]"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-12 text-center mt-4">
                    <el-button size="medium" :loading="loading_submit" type="primary" @click="saveCompany">
                        Siguiente</el-button>
                </div>
            </div>

            <!-- Certificado -->
            <div class="row" v-show="active == 1">
                <div class="col-md-8">
                    <form autocomplete="off">
                        <div class="form-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group" :class="{'has-danger': errors.password}">
                                        <label class="control-label">Password</label>
                                        <el-input type="password" v-model="form.password"></el-input>
                                        <small class="form-control-feedback" v-if="errors.password"
                                            v-text="errors.password"></small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group" :class="{'has-danger': errors.certificate}">
                                        <textarea hidden id="base64" rows="5"></textarea>
                                        <label class="control-label">File</label>
                                        <el-upload ref="fileCertificado" :auto-upload="false" width="100px"
                                            :on-change="handleChangeFileCertificado" :limit="1" drag action="''">
                                            <i class="el-icon-upload"></i>
                                            <div class="el-upload__text">
                                                Suelta tu archivo aquí o
                                                <em>haz clic para cargar</em>
                                            </div>
                                            <div slot="tip" class="el-upload__tip">Solo archivos .pfx</div>
                                        </el-upload>
                                        <small class="form-control-feedback" v-if="errors.certificate"
                                            v-text="errors.certificate"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-12 text-center mt-4">
                    <el-button size="medium" type="primary" :loading="loading_submit" @click="saveCertificate">
                        Finalizar</el-button>
                </div>
            </div>
        </div>
    </div>
</template>
<style>
    .extend {
        width: 100%;
    }
</style>
<script>
    function handleFileSelect(file) {
        var f = file; //evt.target.files[0]; // FileList object
        var reader = new FileReader();
        // Closure to capture the file information.
        reader.onload = (function (theFile) {
            return function (e) {
                var binaryData = e.target.result;
                //Converting Binary Data to base 64
                var base64String = window.btoa(binaryData);
                //showing file converted to base64
                document.getElementById("base64").value = base64String;
                console.log(
                    "File converted to base64 successfuly!\nCheck in Textarea hidden"
                );
                //return base64String;
            };
        })(f);
        // Read in the image file as a data URL.
        reader.readAsBinaryString(f);
    }
    import 'element-ui/lib/theme-chalk/index.css';
    export default {
        components: {},
        data() {
            return {
                hostname: window.location.hostname,
                loading_submit: false,
                active: 0,
                errors: [],
                resource: "configuration",
                resourceapi: "api/ubl2.1/config",
                type_document_identification: [],
                type_organization: [],
                type_regime: [],
                department: [],
                municipality: [],
                municipality_filter: [],
                type_document: [],
                type_liability: [],
                form: {},
                responseCompany: {},
                responseSoftware: {},
                responseCertificate: {},
                responseResolution: {}
            };
        },
        created() {
            this.getTables();
            this.initForm()
        },
        methods: {
            initForm() {
                this.form = {
                    generated_to_date: 0,
                };
                this.responseCompany = {};
                this.responseSoftware = {};
                this.responseCertificate = {};
                this.responseResolution = {};
            },
            getHeaderConfig() {
                let token = this.responseCompany.token;
                let axiosConfig = {
                    headers: {
                        "Content-Type": "application/json;charset=UTF-8",
                        Accept: "application/json",
                        Authorization: `Bearer ${token}`
                    }
                };
                return axiosConfig;
            },
            saveCompany() {
                this.loading_submit = true;
                return new Promise((resolve, reject) => {
                    this.$http
                        .post(
                            `/${this.resourceapi}/${this.form.nit}/${this.form.dv}`,
                            this.form
                        )
                        .then(response => {
                            if (response.data.success) {
                                this.responseCompany = response.data;
                                this.$message.success(response.data.message);
                                this.next();
                            } else {
                                this.$message.error(response.data.message);
                            }
                        })
                        .catch(error => {
                            if (error.response.status === 422) {
                                this.errors = error.response.data.errors;
                            } else {
                                this.$message.error(error.response.data.message);
                                this.initForm()
                            }
                        })
                        .then(() => {
                            this.loading_submit = false;
                        });
                });
            },
            saveCertificate() {
                this.loading_submit = true;
                return new Promise((resolve, reject) => {
                    this.form.certificate = document.getElementById("base64").value;
                    this.$http
                        .put(
                            `/${this.resourceapi}/certificate`,
                            this.form,
                            this.getHeaderConfig()
                        )
                        .then(response => {
                            if (response.data.success) {
                                this.responseCertificate = response.data;
                                this.$message.success(response.data.message);
                                this.initForm()
                                this.next();
                            } else {
                                this.$message.error(response.data.message);
                            }
                        })
                        .catch(error => {
                            if (error.response.status === 422) {
                                this.errors = error.response.data.errors;
                            } else {
                                this.$message.error(error.response.data.message);
                            }
                        })
                        .then(() => {
                            this.loading_submit = false;
                        });
                });
            },
            filterMunicipality() {
                this.municipality_filter = [];
                let id = this.form.department_id;
                this.municipality_filter = this.municipality.filter(
                    x => x.department_id == id
                );
                //this.form.municipality_id = ''
            },
            handleChangeFileCertificado(file) {
                // this.fileCertificado = file.raw;
                handleFileSelect(file.raw);
                //console.log(dato)
            },
            next() {
                if (this.active++ > 1) this.active = 0;
            },
            getTables() {
                return new Promise((resolve, reject) => {
                    this.$http
                        .get(`/${this.resource}/tables`)
                        .then(response => {
                            this.type_document_identification =
                                response.data.type_document_identification;
                            this.type_organization = response.data.type_organization;
                            this.type_regime = response.data.type_regime;
                            this.department = response.data.department;
                            this.municipality = response.data.municipality;
                            this.type_document = response.data.type_document;
                            this.type_liability = response.data.type_liability;
                        })
                        .catch(error => {})
                        .then(() => {});
                });
            }
        }
    };
</script>
<style>
    .form-control-feedback {
        color: red;
    }
    .el-step__icon.is-text{
        border: none !important;
    }
</style>
