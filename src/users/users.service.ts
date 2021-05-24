import { Injectable } from '@nestjs/common'
import { UserRepository } from './user.repository'

@Injectable()
export class UsersService {
  constructor(private userRepository: UserRepository) {}

  async findByUsername(username: string) {
    return await this.userRepository.findOne({
      where: {
        username,
      },
    })
  }
}
