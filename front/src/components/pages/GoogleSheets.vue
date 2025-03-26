<template>
	<div id="loading_placeholder" class="d-flexalign-items-center justify-content-center vh-100" v-if="isLoading">
		<loader-circle></loader-circle>
	</div>
	<div id="content" v-if="!isLoading" class="">
		<a href="/" class="btn btn-light mb-3 ">Обратно к авторизации</a>
		<div class="p-3 border rounded w-50">
			<div class="actions_block mb-3">
				<input id="tableIdInput" type="text" :class="{ 'form-control': true, 'input-error': showErr }"
					placeholder="Скопируйте сюда ссылку на таблицу" v-model="tableLink">
				<transition name="fade">
					<div v-show="showErr" class="ms-1 text-danger position-absolute" style="white-space: pre-line;">
						{{ inputMessage }}
					</div>
				</transition>
				<div class="d-flex flex-row justify-content-between align-items-center mt-3">
					<div class="d-flex align-items-center gap-2">
						<span>Отобразить строк:</span>
						<select class="form-select" style="width: auto;" aria-label="Default select example" v-model="limit">
							<option value="25">25</option>
							<option value="100">100</option>
							<option value="200">200</option>
						</select>
					</div>
					
					<div class="">
						<a @click.prevent="fetchTable" class="btn btn-dark ">Fetch Table</a>
					</div>
				</div>
				
			</div>
			<div class="table-scroll">
				<table class="table">
					<thead class="table-dark" >
						<tr>
							<th scope="col" v-for="column in table[0].slice(0, -1)" :key="column">{{ column }}</th>
							<th scope="col" class="text-end">actions</th>
						</tr>
					</thead>
					<tbody>
						<table-row v-for="(row, rowIndex) in table.slice(1)" :key="row" :data="row" :rowIndex="rowIndex"
							@deleteItem="deleteItem" @updateItem="showUpdateForm"></table-row>
					</tbody>
				</table>
			</div>
			<div v-if="fetchingTable" class="d-flex justify-content-center mt-3">
				<loader-circle></loader-circle>
			</div>

			<div v-if="calcNonEmptyTable && pageLimit > 1" class="d-flex justify-content-center mt-3">
				<ul class="pagination" v-if="pageLimit < 8">
					<li class="page-item"><a class="page-link" href="#">Previous</a></li>
					<li class="page-item" v-for="i in pageLimit" :key="i"><a class="page-link" :class="{'active': page == i}" href="#"  @click="page = i; fetchTable()" >{{ i }}</a></li>
					<li class="page-item"><a class="page-link" href="#">Next</a></li>
				</ul>
				<ul class="pagination" v-if="pageLimit >= 8">
					<li class="page-item" v-if="1 - page < -5"><a class="page-link" href="#" @click="page = 1; fetchTable()">1</a></li>
					<li class="page-item" v-if="1 - page < -3"><a class="page-link" href="#" @click="page = page - 2; fetchTable()">{{ '<<' }}</a></li>
					<li class="page-item" v-if="1 - page < -2"><a class="page-link" href="#" @click="page--; fetchTable()">{{ '<' }}</a></li>
					<li class="page-item" v-for="i in pagesToDisplay" :key="i"><a class="page-link" :class="{'active': page == i}" href="#"  @click="page = i; fetchTable()" >{{ i }}</a></li>
					<li class="page-item" v-if="pageLimit - page > 0"><a class="page-link" href="#" @click="page++; fetchTable()">{{ '>' }}</a></li>
					<li class="page-item" v-if="pageLimit - page > 1"><a class="page-link" href="#" @click="page = page + 2; fetchTable()">{{ '>>' }}</a></li>
					<li class="page-item" v-if="pageLimit - page > 3"><a class="page-link" href="#" @click="page = pageLimit; fetchTable()">{{ pageLimit }}</a></li>

				</ul>
			</div>

			<transition name="fade">
				<div class="d-flex justify-content-center" v-if="calcNonEmptyTable">
					<a @click.prevent="newRow" class="btn btn-dark me-1" data-bs-toggle="modal" data-bs-target="#modalWindow">Добавить новую строку</a>

					<div class="dropdown  dropup">
						<button class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown"
							aria-expanded="false">
							<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor"
								class="bi bi-list" viewBox="0 0 16 16">
								<path fill-rule="evenodd"
									d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5" />
							</svg>
						</button>
						<ul class="dropdown-menu border-2">
							<li><a class="dropdown-item fw-bolder" href="#" @click="importToDB">Импортировать гугл таблицу в бд</a></li>
							<li><a class="dropdown-item fw-bolder" href="#" @click="eraseTable">Очистить таблицу в бд</a></li>
							<li><a class="dropdown-item fw-bolder" href="#" @click="seedTable">Сгенерировать 1000 строк в бд</a></li>
							<div class="dropdown-divider"></div>
							<li><a class="dropdown-item fw-bolder" href="#" @click="eraseGoogleTable">Очистить гугл таблицу</a></li>
							<li><a class="dropdown-item fw-bolder" href="#" @click="importToGoogleTable">Импортировать бд в гугл таблицу</a></li>
						</ul>
					</div>
				</div>
			</transition>

			<transition name="fade">
				<div v-if="calcRequestMessage">
					<div role="alert" class="mt-3"
						:class="{ 'alert': true, 'alert-warning': requestmsg.requestPending, 'alert-success': requestmsg.requestSuccess, 'alert-danger': requestmsg.requestError }">
						{{ displayRequestStatus }}
					</div>
				</div>
			</transition>

		</div>

		<row-creation-form :fields="table[0].slice(0, -1)" @createNewRow="createNewRow"></row-creation-form>
		<modal-with-slot :modalName="'modalEdit'">
			<template v-slot:header>
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="staticBackdropLabel">Изменение строки c адресом A{{
						currentlyEditingRow[3] + 1 }}</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
			</template>
			<template v-slot:body>
				<div class="modal-body">
					<div id="input-block" v-for="(field, fieldIndex) in table[0].slice(0, -1)" :key="field">
						<label for="basic-url" class="form-label">Введите {{ field }}</label>
						<div class="input-group mb-3">
							<input type="text" :placeholder="currentlyEditingRow[fieldIndex]" class="form-control"
								v-model="currentlyEditingRow[fieldIndex]">
						</div>
					</div>
				</div>
			</template>
			<template v-slot:footer>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" @click="test">++</button>

					<button type="button" class="btn btn-danger" data-bs-dismiss="modal">Отменить</button>
					<button type="button" class="btn btn-dark" data-bs-dismiss="modal"
						@click.prevent="updateItem()">Добавить</button>
				</div>
			</template>
		</modal-with-slot>


	</div>
</template>

<script>
import axios from "axios"
import LoaderCircle from "../anims/LoaderCircle.vue";
import RowCreationForm from "../ui/RowCreationForm.vue";
import TableRow from "../ui/table/TableRow.vue";
import ModalWithSlot from "../ui/ModalWithSlot.vue";

export default {
	name: 'App',
	data() {
		return {
			inputMessage: '',
			requestmsg: {
				requestPending: false,
				requestSuccess: false,
				requestError: false
			},

			showModal: false,
			showErr: false,
			isLoading: false,
			fetchingTable: false,

			tableLink: null,
			table: [['#', '#', '#']],

			limit: 25,
			page: 1,
			pageLimit: 5,

			currentlyEditingRow: [1, 2, 3, 4]
		}
	},
	components: {
		LoaderCircle, RowCreationForm, TableRow, ModalWithSlot
	},
	methods: {
		switchRequestStatus(newStatus) {
			this.requestmsg.requestError = false
			this.requestmsg.requestPending = false
			this.requestmsg.requestSuccess = false

			this.requestmsg[newStatus] = true
		},
		async fetchTable() {

			if (this.tableLink == null) {
				this.inputMessage = 'Заполните это поле'
				this.showErr = true
				setTimeout(() => {
					this.showErr = false
				}, 2000)
				return
			}

			try {
				this.fetchingTable = true

				const response = await axios.post(process.env.VUE_APP_BACKEND_URL + '/sheets/get/' + this.tableLink.match(/\/d\/([a-zA-Z0-9-_]+)/)[1],
					{ 
						'access_token': this.getCookie('access_token'),
						'limit': this.limit, 
						'page': this.page
					},
					{
						headers: {
							'X-XSRF-TOKEN': this.getCookie('XSRF-TOKEN')
						},
						withCredentials: true
					}
				)

				if (response.data.error == undefined && Array.isArray(response.data.table)) {
					response.data.table = response.data.table.reduce((acc, row, index) => {
						if (!row.every(elem => elem === '')) {
							acc.push([...row, index]);
						}
						return acc;
					}, []);

					this.table = response.data.table
					this.pageLimit = (response.data.tablesize.totalRows - 1) / this.limit
				} else {
					throw Error
				}
			} catch (err) {
				console.log(err)

				this.inputMessage = 'Произошла ошибка. Попробуйте пройти авторизацию заново. \n Или попробуйте ввести другую ссылку.'
				this.showErr = true
				setTimeout(() => {
					this.showErr = false
				}, 4000)
			}
			this.fetchingTable = false

		},
		async createNewRow(data) {

			const newRow = structuredClone(data)

			this.requestmsg.requestError = false
			this.requestmsg.requestSuccess = false
			this.requestmsg.requestPending = true

			try {
				const response = await axios.post(process.env.VUE_APP_BACKEND_URL + '/sheets/update/' + this.tableLink.match(/\/d\/([a-zA-Z0-9-_]+)/)[1], {
					access_token: this.getCookie('access_token'),
					table: data
				}, {
					headers: {
						'X-XSRF-TOKEN': this.getCookie('XSRF-TOKEN')
					},
					withCredentials: true
				})

				if (response.data.status == 'error' || response.data.error != undefined) {
					throw new Error('error occured')
				}
				if (response.data.status === 'success') {
					newRow.push(this.table.length)

					this.table.push(newRow)
				}
			} catch (err) {
				this.requestmsg.requestPending = false
				this.requestmsg.requestError = true
			}

			this.requestmsg.requestPending = false
			this.requestmsg.requestSuccess = true
		},
		async deleteItem(emitData) {
			this.requestmsg.requestError = false
			this.requestmsg.requestSuccess = false
			this.requestmsg.requestPending = true


			// await axios.post('https://sheets.googleapis.com/v4/spreadsheets/1BqR1iknD9yGx8jP5jqX_CuUlbPGiuB299oOs5hOJgVI/values/A10:ZZ10:clear?key=', {

			// 	}, {
			// 		headers: {
			// 			'Authorization': 'Bearer ' + this.getCookie('access_token')
			// 		},
			// 	})

			try {
				const response = await axios.post(process.env.VUE_APP_BACKEND_URL + '/sheets/slice/' + this.tableLink.match(/\/d\/([a-zA-Z0-9-_]+)/)[1], {
					access_token: this.getCookie('access_token'),
					indexToRemove: emitData + 2,
				}, {
					headers: {
						'X-XSRF-TOKEN': this.getCookie('XSRF-TOKEN')
					},
					withCredentials: true
				})

				if (response.data.status == 'error' || response.data.error != undefined) {
					throw new Error('error occured')
				}
			} catch (err) {
				this.requestmsg.requestPending = false
				this.requestmsg.requestError = true
			}

			this.table.splice(emitData, 1)
			this.requestmsg.requestPending = false
			this.requestmsg.requestSuccess = true
		},
		showUpdateForm(emitData) {
			this.currentlyEditingRow = structuredClone(emitData)
			window.$('#modalEdit').modal('show')
		},
		async updateItem() {
			this.switchRequestStatus('requestPending')

			try {
				const response = await axios.post(process.env.VUE_APP_BACKEND_URL + '/sheets/edit/' + this.tableLink.match(/\/d\/([a-zA-Z0-9-_]+)/)[1], {
					access_token: this.getCookie('access_token'),
					row: this.currentlyEditingRow
				},
					{
						headers: {
							'X-XSRF-TOKEN': this.getCookie('XSRF-TOKEN')
						},
						withCredentials: true
					})
				if (response.status == 200) {
					const IndexOfEditedRow = this.table.findIndex((elem) => elem[elem.length - 1] == this.currentlyEditingRow[this.currentlyEditingRow.length - 1])
					this.table[IndexOfEditedRow] = structuredClone(this.currentlyEditingRow)
					this.switchRequestStatus('requestSuccess')
				}

			} catch (err) {
				this.switchRequestStatus('requestError')
				console.log(err)
			}
		},
		async importToDB() {
			try {
				await axios.post(process.env.VUE_APP_BACKEND_URL + '/sheets/database/import', {
					
					data: this.table
				},
					{
						headers: {
							'X-XSRF-TOKEN': this.getCookie('XSRF-TOKEN')
						},
						withCredentials: true
					})
			} catch(err) {
				console.log(err)
			}
		},
		async eraseTable() {
			try {
				await axios.post(process.env.VUE_APP_BACKEND_URL + '/sheets/database/erase', {
					access_token: this.getCookie('access_token')
				},
					{
						headers: {
							'X-XSRF-TOKEN': this.getCookie('XSRF-TOKEN')
						},
						withCredentials: true
					})
			} catch(err) {
				console.log(err)
			}
		},
		async eraseGoogleTable() {
			try {
				const response = await axios.post(process.env.VUE_APP_BACKEND_URL + '/sheets/erase/' + this.tableLink.match(/\/d\/([a-zA-Z0-9-_]+)/)[1], {
					access_token: this.getCookie('access_token'),
					table_rows_count: this.table.length
				}, {
					headers: {
						'X-XSRF-TOKEN': this.getCookie('XSRF-TOKEN')
					},
					withCredentials: true
				})

				if (response.data.status == 'error' || response.data.error != undefined) {
					throw new Error('error occured')
				}
			} catch (err) {
				console.log(err)
				
			}
		},
		async seedTable() {
			try {
				await axios.post(process.env.VUE_APP_BACKEND_URL + '/sheets/database/seed', {}, {				
					headers: {
						'X-XSRF-TOKEN': this.getCookie('XSRF-TOKEN')
					},
					withCredentials: true
				})
			} catch(err) {
				console.log(err)
			}
		},
		async importToGoogleTable() {
			this.switchRequestStatus('requestPending')

			try {
				const response = await axios.post(process.env.VUE_APP_BACKEND_URL + '/sheets/fill/' + this.tableLink.match(/\/d\/([a-zA-Z0-9-_]+)/)[1], {
					access_token: this.getCookie('access_token'),
				},
					{
						headers: {
							'X-XSRF-TOKEN': this.getCookie('XSRF-TOKEN')
						},
						withCredentials: true
					})
				if (response.status == 200) {
					this.switchRequestStatus('requestSuccess')
				}

			} catch (err) {
				this.switchRequestStatus('requestError')
				console.log(err)
			}
		}

	},
	watch: {
		requestmsg: {
			handler() {
				clearTimeout(this.timeout)
				this.timeout = setTimeout(() => {
					this.requestmsg = {
						requestPending: false,
						requestSuccess: false,
						requestError: false,
					}
				}, 6000)
			},
			deep: true
		},
		limit() {
			this.page = 1
			this.fetchTable()
		}
	},
	async mounted() {

		this.isLoading = true
		// await new Promise(resolve => setTimeout(resolve, 5000));
		this.isLoading = false
	},
	computed: {
		calcNonEmptyTable() {
			if (this.table[0][0] == '#') {

				return false
			} else {

				return true
			}
		},
		calcRequestMessage() {
			return Object.values(this.requestmsg).some(value => value === true)
		},
		displayRequestStatus() {
			// eslint-disable-next-line no-unused-vars
			const active = Object.entries(this.requestmsg).find(([key, value]) => value === true) //finds first true elem
			if (active == undefined) {
				return
			}
			switch (active[0]) {
				case 'requestPending':
					return 'Запрос обратывается. Ожидайте.'
				case 'requestSuccess':
					return 'Изменения сохранены.'
				case 'requestError':
					return 'Произошла ошибка. Изменения не сохранены.'
				default:
					return 'foo'
			}
		},
		pagesToDisplay() {
			return [...Array(this.pageLimit).keys()].map(n => n + 1).filter(i => Math.abs(i - this.page) <= 3)
		}
	}
}
</script>

<style>
.input-error {
	border-color: rgb(255, 101, 101) !important;
	background-color: rgb(255, 233, 233) !important;
}

.input-error:focus {
	border-color: rgb(255, 101, 101) !important;
	box-shadow: 0 0 0 0.2rem rgba(255, 101, 101, 0.5) !important;
}

.fade-enter-active,
.fade-leave-active {
	transition: opacity 0.5s ease, transform 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
	opacity: 0;
	transform: translateY(-5px);

}

.fade-enter-to,
.fade-leave-from {
	opacity: 1;
	transform: translateY(0);

}

.alert {
	transition: background-color 0.5s;
}

#content {
	display: flex;
	justify-content: center;
	align-items: center;
	height: 100vh;
	width: 100vw;
	flex-direction: column;
}

.table-scroll {
	max-height: 60vh;
	overflow-y: auto;
	width: 100%;
}

.table thead{
	border-bottom: 0;
}

.table thead th:first-child {
  border-top-left-radius: 5%;
  border-bottom-left-radius: 5px;
}

.table thead th:last-child {
  border-top-right-radius: 5px;
  border-bottom-right-radius: 5px;
}

.dropdown-item:active {
	background-color: rgb(184, 184, 184) !important;
}

td {
    word-wrap: break-word;  
    white-space: normal; 
    overflow-wrap: break-word; 
    max-width: 200px;
}

.form-select {
    width: auto !important;
    min-width: unset !important;
}

.page-link {
	color: #212529 !important;
}

.page-link:focus {
	border-color: #8b9aa8 !important;
	box-shadow: 0 0 0 0.2rem #d3d3d3 !important;
	background-color: #ebebeb !important;
}

.page-link.active {
	background-color: #e4e4e4 !important;
	border-color: #b3b3b3 !important;
}
</style>