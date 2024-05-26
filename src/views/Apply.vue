<template>
    <div class="container">
        <div class="work">
            <div class="work-step">
                <p class="work-step_text" v-if="this.$route.params.step == 'about'">
                    Розкажи про себе
                </p>
                <router-link class="work-step_text" :to="`/apply/${this.$route.params.step == 'cv' ? 'about' : 'cv'}`" v-else>
                    <span class="pi pi-arrow-left"></span>{{ this.$route.params.step == 'cv' ? 'Маєш готове резюме *' : 'Досвід роботи' }}
                </router-link>
                <div class="work-step_line">
                    <span v-for="item in steps" :key="item" :style="item == this.$route.params.step ? 'background: #9ea108;' : ''"></span>
                </div>
            </div>
            <form class="work-form">
                <div v-if="this.$route.params.step == 'about'">
                    <label for="name" class="work-label">
                        Як тебе звати *
                        <InputText class="work-input" type="text" name="name" id="name" v-model="this.name"/>
                    </label>
                    <label for="city" class="work-label">
                        Місто в якому ти зараз живеш *
                        <InputText class="work-input" type="text" name="city" id="city" v-model="this.city"/>
                    </label>
                    <label for="type_to_work" class="work-label">
                        В якому напрямку хочеш працювати *
                        <Dropdown class="work-input work-input_dropdown" v-model="this.selectedType" :options="typeList" optionLabel="name"/>
                    </label>
                    <label for="age" class="work-label">
                        Скільки тобі повних років *
                        <InputNumber class="work-input" v-model="this.age" inputId="age" locale="ua-UA"/>
                    </label>
                    <label for="phone" class="work-label">
                        Номер телефону *
                        <InputMask class="work-input" v-model="this.phone" id="phone" placeholder="+380" mask="+380 (99) 99 99 999"/>
                    </label>
                    <label for="email" class="work-label">
                        Вкажи свою електрону пошту *
                        <InputText class="work-input" type="text" name="email" id="email" v-model="this.email"/>
                    </label>
                    <Button label="Продовжити" @click="nextStep('cv')" />
                </div>
                <div v-if="this.$route.params.step == 'cv'">
                    <label for="cv-yes" class="ml-2">
                        <RadioButton class="work-radio" v-model="this.cv" inputId="cv-yes" name="cv-yes" :value="true" />
                        Так
                    </label>
                    <label for="cv-no" class="ml-2">
                        <RadioButton class="work-radio" v-model="this.cv" inputId="cv-no" name="cv-no" :value="false" />
                        Ні
                    </label>
                    <label for="file" v-if="this.cv" class="work-file">
                        <input type="file" id="file" @change="uploadCv($event)" accept=".pdf, .doc, .docx, .png" />
                    </label>
                    <Button :loading="buttonLoader">
                        <span v-if="this.cv" @click="sendMail()">Відправити</span>
                        <span v-if="!this.cv" @click="nextStep('work')">Продовжити</span>
                    </Button>
                </div>
                <div v-if="this.$route.params.step == 'work'">
                    <label for="company" class="work-label">
                        Компанія *
                        <InputText class="work-input" type="text" name="company" id="company" v-model="this.company"/>
                    </label>
                    <label for="position" class="work-label">
                        Посада *
                        <InputText class="work-input" type="text" name="position" id="position" v-model="this.position"/>
                    </label>
                    <label for="startDate" class="work-label">
                        Початок роботи *
                        <Calendar dateFormat="dd.mm.yy" class="work-input" v-model="this.startDate" :maxDate="new Date()" id="startDate"/>
                    </label>
                    <label for="startDate" class="work-label">
                        Кінець *
                        <Calendar dateFormat="dd.mm.yy" class="work-input" v-model="this.endDate" id="startDate"/>
                    </label>
                    <label for="phone" class="work-label work-textarea">
                        Коментар (за бажанням)
                        <Editor v-model="this.comment" editorStyle="height: 150px" />
                    </label>
                    <Button label="Додати ще посаду" icon="pi pi-plus" @click="addNewPosition()"/>
                    <Button label="Відправити" :loading="buttonLoader" @click="sendMail()"/>
                    <div v-if="this.showExp">
                        <p>
                            Додані місця роботи:
                        </p>
                        <div v-for="work in this.lastWork.filter(item => item.company != '')" :key="work.id">
                            {{ work }}
                            <span class="pi pi-trash work-delete" @click="deletePosition(work.id)"></span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import InputNumber from 'primevue/inputnumber';
import InputMask from 'primevue/inputmask';
import RadioButton from 'primevue/radiobutton';
import Calendar from 'primevue/calendar';
import Editor from 'primevue/editor';
import axios from 'axios';
import moment from 'moment';
export default {
    components: {Button, InputText, Dropdown, InputNumber, InputMask, RadioButton, Calendar, Editor},
    data(){
        return{
            steps: ['about', 'cv', 'work'],
            typeList: [
                {name: 'Продаж'},
                {name: 'Купівля'}
            ],
            name: '',
            city: '',
            selectedType: {name: 'Продаж'},
            age: '',
            phone: '',
            email: '',
            cv: true,
            cvFile: '',
            positions: 1,
            lastWork: [],
            company: '',
            position: '',
            startDate: '',
            endDate: '',
            comment: '',
            showExp: false,
            buttonLoader: false
        }
    },
    methods:{
        nextStep(step) {
            if(
                this.name.trim() !== '' &&
                this.city.trim() !== '' &&
                this.selectedType.name !== '' &&
                this.age !== 0 &&
                this.phone.length === 19 &&
                this.isEmailValid(this.email))
            {
                this.$router.push('/apply/' + step);
            }else{
                this.$store.dispatch('setMessages', [{ severity: 'error', content: 'Заповніть всі обов`язкові поля', id: this.count++}])
            }
        },
        isEmailValid(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        },
        uploadCv(e){
            this.cvFile = e.target.files[0]
        },
        addNewPosition(){
            if(this.company.trim() && this.startDate && this.endDate && this.position.trim()){
                if(this.startDate <= this.endDate){
                    const work = {
                        id: this.lastWork.length,
                        company: this.company.trim(),
                        position: this.position.trim(),
                        startDate: moment(this.startDate).format('YYYY-MM-DD'),
                        endDate: moment(this.endDate).format('YYYY-MM-DD'),
                        comment: this.comment
                    }
                    this.lastWork.push(work)
                    this.company = ''
                    this.position = ''
                    this.startDate = ''
                    this.endDate = ''
                    this.comment = ''
                    this.showExp = true
                }else{
                    this.$store.dispatch('setMessages', [{ severity: 'error', content: 'Дата закінчення роботи не може бути меншою за початок!', id: this.count++}])
                }
            }else{
                this.$store.dispatch('setMessages', [{ severity: 'error', content: 'Заповніть всі обов`язкові поля', id: this.count++}])
            }
        },
        deletePosition(id){
            this.lastWork = this.lastWork.filter(item => item.id != id)
        },
        saveUserInfo() {
            const userInfo = JSON.stringify([{
                name: this.name.trim(),
                city: this.city.trim(),
                selectedCity: this.selectedCity,
                age: this.age,
                phone: this.phone,
                email: this.email,
                cv: this.cv,
                company: this.company.trim(),
                position: this.position.trim(),
                startDate: moment(this.startDate).format('YYYY-MM-DD'),
                endDate: moment(this.endDate).format('YYYY-MM-DD'),
                comment: this.comment
            }])
            localStorage.setItem('user', userInfo);
        },
        sendMail(){
            this.showExp = false
            if(this.name && this.city && this.selectedType.name && this.age && this.phone && this.email){
                if(!this.cv){
                    if(this.company.trim() && this.startDate && this.endDate && this.position.trim()){
                        this.lastWork.push({
                            id: this.lastWork.length,
                            company: this.company.trim(),
                            position: this.position.trim(),
                            startDate: moment(this.startDate).format('YYYY-MM-DD'),
                            endDate: moment(this.endDate).format('YYYY-MM-DD'),
                            comment: this.comment
                        })
                    }else{
                        this.$store.dispatch('setMessages', [{ severity: 'error', content: 'Заповніть всі обов`язкові поля', id: this.count++}])
                        return
                    }
                    
                }else if(!this.cvFile){
                    this.$store.dispatch('setMessages', [{ severity: 'error', content: 'Додайте резюме', id: this.count++}])
                    return;
                }
                this.buttonLoader = true
                let config = {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }
                let formData = new FormData();
                formData.append('name', this.name.trim());
                formData.append('city', this.city.trim());
                formData.append('type', this.selectedType.name);
                formData.append('age', this.age);
                formData.append('phone', this.phone);
                formData.append('email', this.email);
                formData.append('cv', this.cvFile);
                formData.append('expirience', JSON.stringify(this.lastWork));
                axios.post('https://test.enott.com.ua/api/job', formData, config)
                .then(response => {
                    this.$store.dispatch('setMessages', [{ severity: 'success', content: 'Повідомлення відправлене', id: this.count++}])
                    this.name = ''
                    this.city = ''
                    this.selectedType = {name: 'Продаж'}
                    this.age = ''
                    this.phone = ''
                    this.email = ''
                    this.cv = true
                    this.cvFile = ''
                    this.positions = 1
                    this.lastWork = []
                    this.company = ''
                    this.position = ''
                    this.startDate = ''
                    this.endDate = ''
                    this.comment = ''
                    this.buttonLoader = false
                    this.$router.push({ path : '/' });
                    localStorage.removeItem('user');
                })
                .catch(error => {
                    this.$store.dispatch('setMessages', [{ severity: 'error', content: error.response.data.error, id: this.count++}])
                });
            }else{
                this.$store.dispatch('setMessages', [{ severity: 'error', content: 'Заповніть всі обов`язкові поля', id: this.count++}])
            }
        }
    },
    mounted(){
        window.addEventListener('beforeunload', this.saveUserInfo);
        const savedUserInfo = localStorage.getItem('user');
        if (savedUserInfo) {
            const userInfo = JSON.parse(savedUserInfo);
            if (userInfo.length > 0) {
                const user = userInfo[0];
                this.name = user.name;
                this.city = user.city;
                this.selectedCity = user.selectedCity;
                this.age = user.age;
                this.phone = user.phone;
                this.email = user.email;
                this.cv = user.cv;
                this.company = user.company
                this.position = user.position
                this.startDate = user.startDate && user.startDate != 'Invalid date' ? user.startDate : new Date()
                this.endDate = user.endDate && user.endDate != 'Invalid date' ? user.endDate : new Date()
                this.comment = user.comment
            }
        }
    },
    beforeDestroy() {
        this.saveUserInfo();
        window.removeEventListener('beforeunload', this.saveUserInfo);
    }
}
</script>

<style>

</style>