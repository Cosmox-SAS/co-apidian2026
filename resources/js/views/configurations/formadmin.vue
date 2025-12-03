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
                <div class="col-md-4">
                    <div class="card-body card p-3 text-center border-dashed">
                        <h5 class="mb-3">Subir Ficha RUT (PDF)</h5>

                        <el-upload
                            ref="rutUpload"
                            drag
                            action="#"
                            :auto-upload="false"
                            :limit="1"
                            accept="application/pdf"
                            :on-change="onRutChange"
                            :show-file-list="false"
                            class="rut-drag"
                        >
                            <i class="el-icon-upload"></i>
                            <div class="el-upload__text">
                                Arrastra tu archivo aquí<br>
                                <em>o haz clic para seleccionar</em>
                            </div>
                            <div slot="tip" class="el-upload__tip">Solo archivos PDF</div>
                        </el-upload>

                        <div v-if="selectedRutName" class="uploaded-wrapper mt-3">

                            <!-- CABECERA: Icono + Nombre archivo + X -->
                            <div class="uploaded-header d-flex align-items-center justify-content-between">
                                
                                <!-- Icono + texto -->
                                <div class="d-flex align-items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="pdf-icon" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                    </svg>
                                    <span class="uploaded-name">{{ selectedRutName }}</span>
                                </div>

                                <!-- Botón X -->
                                <button type="button" class="close file-close" @click="resetRutUpload">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>

                            <!-- PIE: Barra de progreso -->
                            <div class="uploaded-footer mt-2">
                                <el-progress
                                    :percentage="rutProgress"
                                    :status="rutProgress == 100 ? 'success' : null"
                                    :stroke-width="8">
                                </el-progress>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FORMULARIO EMPRESA -->
                <div class="col-md-8">
                    <form autocomplete="off">
                        <div class="form-body">
                            <div class="row">

                                <!-- tipo de documento -->
                                <div class="col-md-6">
                                    <div class="form-group" :class="{'has-danger': errors.type_document_identification_id}">
                                        <label>Tipo de Documento</label>
                                        <el-select class="extend" v-model="form.type_document_identification_id" filterable>
                                            <el-option
                                                v-for="option in type_document_identification"
                                                :key="option.id"
                                                :value="option.id"
                                                :label="option.name"
                                            ></el-option>
                                        </el-select>
                                        <small v-if="errors.type_document_identification_id" class="form-control-feedback">
                                            {{ errors.type_document_identification_id[0] }}
                                        </small>
                                    </div>
                                </div>

                                <!-- nit -->
                                <div class="col-md-6">
                                    <div class="form-group" :class="{'has-danger': errors.nit}">
                                        <label>Número de documento</label>
                                        <el-input v-model="form.nit"></el-input>
                                        <small v-if="errors.nit" class="form-control-feedback">{{ errors.nit[0] }}</small>
                                    </div>
                                </div>

                                <!-- dv -->
                                <div class="col-md-3">
                                    <div class="form-group" :class="{'has-danger': errors.dv}">
                                        <label>DV</label>
                                        <el-input v-model="form.dv"></el-input>
                                        <small v-if="errors.dv" class="form-control-feedback">{{ errors.dv[0] }}</small>
                                    </div>
                                </div>

                                <!-- empresa -->
                                <div class="col-md-9">
                                    <div class="form-group" :class="{'has-danger': errors.business_name}">
                                        <label>Empresa</label>
                                        <el-input v-model="form.business_name"></el-input>
                                        <small v-if="errors.business_name" class="form-control-feedback">
                                            {{ errors.business_name[0] }}
                                        </small>
                                    </div>
                                </div>

                                <!-- resto de campos -->
                                <div class="col-md-6">
                                    <div class="form-group" :class="{'has-danger': errors.merchant_registration}">
                                        <label>Registro Mercantil</label>
                                        <el-input v-model="form.merchant_registration"></el-input>
                                        <small v-if="errors.merchant_registration" class="form-control-feedback">
                                            {{ errors.merchant_registration[0] }}
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group" :class="{'has-danger': errors.phone}">
                                        <label>Teléfono</label>
                                        <el-input v-model="form.phone"></el-input>
                                        <small v-if="errors.phone" class="form-control-feedback">
                                            {{ errors.phone[0] }}
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group" :class="{'has-danger': errors.email}">
                                        <label>Correo Electrónico</label>
                                        <el-input v-model="form.email"></el-input>
                                        <small v-if="errors.email" class="form-control-feedback">
                                            {{ errors.email[0] }}
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group" :class="{'has-danger': errors.address}">
                                        <label>Dirección</label>
                                        <el-input v-model="form.address"></el-input>
                                        <small v-if="errors.address" class="form-control-feedback">
                                            {{ errors.address[0] }}
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group" :class="{'has-danger': errors.department_id}">
                                        <label>Departamento</label>
                                        <el-select class="extend" @change="filterMunicipality" v-model="form.department_id" filterable>
                                            <el-option
                                                v-for="option in department"
                                                :key="option.id"
                                                :value="option.id"
                                                :label="option.name"
                                            ></el-option>
                                        </el-select>
                                        <small v-if="errors.department_id" class="form-control-feedback">
                                            {{ errors.department_id[0] }}
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group" :class="{'has-danger': errors.municipality_id}">
                                        <label>Municipio</label>
                                        <el-select class="extend" v-model="form.municipality_id" filterable>
                                            <el-option
                                                v-for="option in municipality_filter"
                                                :key="option.id"
                                                :value="option.id"
                                                :label="option.name"
                                            ></el-option>
                                        </el-select>
                                        <small v-if="errors.municipality_id" class="form-control-feedback">
                                            {{ errors.municipality_id[0] }}
                                        </small>
                                    </div>
                                </div>

                                <!-- Responsabilidad / Organización / Régimen -->
                                <div class="col-md-4">
                                    <div class="form-group" :class="{'has-danger': errors.type_liability_id}">
                                        <label>Tipo Responsabilidad</label>
                                        <el-select class="extend" v-model="form.type_liability_id" filterable>
                                            <el-option
                                                v-for="option in type_liability"
                                                :key="option.id"
                                                :value="option.id"
                                                :label="option.name"
                                            ></el-option>
                                        </el-select>
                                        <small v-if="errors.type_liability_id" class="form-control-feedback">
                                            {{ errors.type_liability_id[0] }}
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group" :class="{'has-danger': errors.type_organization_id}">
                                        <label>Organización</label>
                                        <el-select class="extend" v-model="form.type_organization_id" filterable>
                                            <el-option
                                                v-for="option in type_organization"
                                                :key="option.id"
                                                :value="option.id"
                                                :label="option.name"
                                            ></el-option>
                                        </el-select>
                                        <small v-if="errors.type_organization_id" class="form-control-feedback">
                                            {{ errors.type_organization_id[0] }}
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group" :class="{'has-danger': errors.type_regime_id}">
                                        <label>Régimen</label>
                                        <el-select class="extend" v-model="form.type_regime_id" filterable>
                                            <el-option
                                                v-for="option in type_regime"
                                                :key="option.id"
                                                :value="option.id"
                                                :label="option.name"
                                            ></el-option>
                                        </el-select>
                                        <small v-if="errors.type_regime_id" class="form-control-feedback">
                                            {{ errors.type_regime_id[0] }}
                                        </small>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>

                    <!-- <div class="text-center mt-4">
                        <el-button size="medium" type="primary" :loading="loading_submit" @click="saveCompany">
                            Siguiente
                        </el-button>
                    </div> -->
                </div>

                <div class="col-md-12 text-center mt-4">
                    <el-button size="medium" type="primary" :loading="loading_submit" @click="saveCompany">
                        Siguiente
                    </el-button>
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
                responseResolution: {},
                selectedRutName: null,
                rutProgress: 0,
                simulatedInterval: null,
                simulatedDuration: 0,
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
                    nit: null,
                    dv: null,
                    business_name: null,
                    merchant_registration: null,
                    phone: null,
                    email: null,
                    address: null,

                    department_id: null,
                    municipality_id: null,

                    type_document_identification_id: null,
                    type_liability_id: null,
                    type_organization_id: null,
                    type_regime_id: null,
                };
                this.responseCompany = {};
                this.responseSoftware = {};
                this.responseCertificate = {};
                this.responseResolution = {};
            },
            validateRequiredFields() {
                const requiredFields = [
                    "nit",
                    "dv",
                    "business_name",
                    "merchant_registration",
                    "phone",
                    "email",
                    "address",
                    "department_id",
                    "municipality_id",
                    "type_document_identification_id",
                    "type_liability_id",
                    "type_organization_id",
                    "type_regime_id"
                ];
                this.errors = {};
                let valid = true;
                requiredFields.forEach(field => {
                    if (!this.form[field]) {
                        this.$set(this.errors, field, ["Campo obligatorio."]);
                        valid = false;
                    }
                });
                return valid;
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
            async saveCompany() {
                if (!this.validateRequiredFields()) {
                    this.$message.warning("Por favor complete todos los campos obligatorios antes de continuar.");
                    return;
                }
                this.loading_submit = true;
                try {
                    const response = await this.$http.post(
                        `/${this.resourceapi}/${this.form.nit}/${this.form.dv}`,
                        this.form
                    );
                    if (response.data.success) {
                        // Mostrar mensaje de éxito
                        this.$message.success(response.data.message);
                        // Guardar datos de la empresa y token para el siguiente paso
                        this.responseCompany = response.data;
                        // Avanzar al siguiente paso
                        this.next();
                    } else {
                        if (response.data.error) {
                            this.$message.error(response.data.error);
                        } else if (response.data.message) {
                            this.$message.error(response.data.message);
                        } else {
                            this.$message.error("Error al crear la empresa.");
                        }
                    }
                } catch (error) {
                    if (error.response && error.response.status === 422) {
                        this.errors = error.response.data.errors;
                        let allErrors = Object.values(this.errors).flat().join('\n');
                        this.$message.error(allErrors);
                    } else if (error.response && error.response.data && error.response.data.error) {
                        this.$message.error(error.response.data.error);
                    } else if (error.response && error.response.data && error.response.data.message) {
                        this.$message.error(error.response.data.message);
                    } else {
                        this.$message.error("Error inesperado al guardar la empresa.");
                    }
                } finally {
                    this.loading_submit = false;
                }
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
            },
            onRutChange(file) {
                const realFile = file.raw;
                this.selectedRutName = realFile.name;
                this.rutProgress = 0;

                if (!realFile) {
                    this.$message.error("No se pudo leer el archivo");
                    return;
                }

                this.initForm();

                // detener intervalos previos por si acaso
                if (this.simulatedInterval) clearInterval(this.simulatedInterval);

                // --- SIMULACIÓN DE PROGRESO LENTO ---
                const steps = 100;
                const stepTime = this.simulatedDuration / steps;

                this.simulatedInterval = setInterval(() => {
                    if (this.rutProgress < 100) {
                        this.rutProgress++;
                    } else {
                        clearInterval(this.simulatedInterval);
                        this.uploadRut(realFile); // cuando termina, ahora sí subimos el archivo
                    }
                }, stepTime);
            },
            resetRutUpload() {
                this.selectedRutName = null;
                this.rutProgress = 0;
                this.initForm();

                if (this.simulatedInterval) {
                    clearInterval(this.simulatedInterval);
                    this.simulatedInterval = null;
                }

                if (this.$refs.rutUpload) {
                    this.$refs.rutUpload.clearFiles();
                }
            },
            uploadRut(realFile) {
                const formData = new FormData();
                formData.append("rut", realFile);

                this.$http.post("/configuration/extract-rut", formData, {
                    headers: { "Content-Type": "multipart/form-data" }
                })
                .then(({ data }) => {

                    if (!data.success) {
                        this.$message.error(data.message);
                        return;
                    }

                    // Llenar formulario con datos reales del backend
                    Object.assign(this.form, data.fields);

                    if (data.fields.department_id) {
                        this.form.department_id = data.fields.department_id;
                        this.filterMunicipality();
                    }

                    if (data.fields.municipality_id) {
                        this.form.municipality_id = data.fields.municipality_id;
                    }

                    this.$message.success("Datos del RUT cargados correctamente");
                    this.validateRutFields();
                })
                .catch(err => {
                    console.error(err);
                    this.$message.error("Error procesando el RUT");
                });
            },
            validateRutFields() {
                this.errors = {}; // limpiar errores previos

                // Campos críticos obligatorios del RUT
                const requiredCritical = [
                    "type_liability_id",
                    "type_regime_id",
                    "type_document_identification_id",
                ];

                requiredCritical.forEach(field => {
                    if (!this.form[field]) {
                        this.$set(this.errors, field, ["Campo obligatorio."]);
                    }
                });

                // Campos generales del formulario que deberían venir del RUT
                const generalFields = [
                    "nit",
                    "dv",
                    "business_name",
                    "merchant_registration",
                    "phone",
                    "email",
                    "address",
                    "department_id",
                    "municipality_id"
                ];

                generalFields.forEach(field => {
                    if (!this.form[field]) {
                        this.$set(this.errors, field, ["Campo no detectado."]);
                    }
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
    .uploaded-file {
        background: #f8f9fa;
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid #e0e0e0;
    }

    .uploaded-file .file-name {
        font-size: 14px;
        color: #333;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .file-close {
        font-size: 20px;
        line-height: 1;
        opacity: 0.6;
        cursor: pointer;
        border: none;
        background: transparent;
    }

    .file-close:hover {
        opacity: 1;
        color: #dc3545;
    }

    /* Contenedor general */
    .uploaded-wrapper {
        border: 1px solid #dcdcdc;
        border-radius: 8px;
        padding: 12px;
        background: #ffffff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    /* Cabecera */
    .uploaded-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Icono PDF */
    .pdf-icon {
        color: #1a1919;
        margin-right: 8px;
    }

    /* Nombre del archivo */
    .uploaded-name {
        font-size: 14px;
        font-weight: 600;
        color: #444;
        max-width: 200px;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    /* Botón X */
    .file-close {
        font-size: 22px;
        opacity: 0.7;
        cursor: pointer;
        border: none;
        background: transparent;
    }

    .file-close:hover {
        opacity: 1;
        color: #dc3545;
    }

    /* Pie */
    .uploaded-footer {
        padding-top: 6px;
    }
    .rut-drag .el-upload-dragger {
        width: 100% !important;
        max-width: 100% !important;
        min-width: 100% !important;
        padding: 20px !important;
    }

    /* Vista móvil */
    @media (max-width: 576px) {
        .rut-drag .el-upload-dragger {
            padding: 16px !important;
            min-height: 120px !important;
        }

        .rut-drag .el-upload__text {
            font-size: 13px !important;
            line-height: 1.3;
        }

        .rut-drag i.el-icon-upload {
            font-size: 28px !important;
        }
    }
</style>
