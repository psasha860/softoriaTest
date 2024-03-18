import { Component } from '@angular/core';
import { FormBuilder, Validators } from '@angular/forms';
import { HttpClient } from '@angular/common/http';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent {
  form = this.fb.group({
    target1: ['', Validators.required],
    target2: ['', Validators.required],
    excludeTarget1: ['', Validators.required],
  });
  responseData: any[] = [];
  message: string = '';
  messageType: 'success' | 'error' = 'success'; // Тип може бути 'success' або 'error'


  constructor(private fb: FormBuilder, private http: HttpClient) {}

  onSubmit(): void {
    if (this.form.valid) {
      const testData = {
        targets: {
          "1": this.form.value.target1,
          "2": this.form.value.target2
        },
        exclude_targets: [this.form.value.excludeTarget1]
      };

      this.http.post<any>('http://localhost:8000/api/competitors', testData).subscribe({
        next: (response) => {
          // Зберігання даних відповіді для відображення
          this.responseData = response.data;
          this.message = response.message;
          this.messageType = response.type;
        },
        error: (error) => {
          console.error('There was an error!', error);
          // Показ помилки, якщо щось пішло не так
          this.message = 'An error occurred!';
          this.messageType = 'error';
        }
      });
    } else {
      // Виведення повідомлення про помилку або підсвічування невалідних полів
      console.log("Form is not valid.");
    }
  }
}
