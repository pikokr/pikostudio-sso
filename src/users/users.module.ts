import { Module } from '@nestjs/common'
import { UsersService } from './users.service'
import { TypeOrmModule } from '@nestjs/typeorm'
import { UserRepository } from './user.repository'

@Module({
  providers: [UsersService],
  imports: [TypeOrmModule.forFeature([UserRepository])],
  exports: [UsersService],
})
export class UsersModule {}
