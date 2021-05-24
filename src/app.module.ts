import { Module } from '@nestjs/common'
import { AppController } from './app.controller'
import { AppService } from './app.service'
import { Oauth2Module } from 'nestjs-oauth2-server'
import { UserLoader } from './oauth2/user.loader'
import { UserValidator } from './oauth2/user.validator'

@Module({
  controllers: [AppController],
  providers: [AppService],
  imports: [
    Oauth2Module.forRoot({
      userLoader: new UserLoader(),
      userValidator: new UserValidator(),
    }),
  ],
})
export class AppModule {}
