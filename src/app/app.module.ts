import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { CreateComponent } from './create/create.component';
import { JoinComponent } from './join/join.component';
import { MasterComponent } from './master/master.component';
import { MemberComponent } from './member/member.component';

@NgModule({
  declarations: [
    AppComponent,
    CreateComponent,
    JoinComponent,
    MasterComponent,
    MemberComponent
  ],
  imports: [
    BrowserModule,
    AppRoutingModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }
