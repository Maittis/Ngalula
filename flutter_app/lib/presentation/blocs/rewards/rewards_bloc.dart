import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../data/models/reward.dart';
import '../../../core/repositories/reward_repository.dart';

// States
abstract class RewardsState {}

class RewardsInitial extends RewardsState {}

class RewardsLoading extends RewardsState {}

class RewardsLoaded extends RewardsState {
  final List<Reward> rewards;
  final List<Reward> achievements;
  RewardsLoaded({required this.rewards, required this.achievements});
}

class RewardRedeemed extends RewardsState {
  final String message;
  RewardRedeemed(this.message);
}

class RewardsError extends RewardsState {
  final String message;
  RewardsError(this.message);
}

// Events
abstract class RewardsEvent {}

class LoadRewards extends RewardsEvent {
  final String userId;
  LoadRewards(this.userId);
}

class RedeemReward extends RewardsEvent {
  final String userId;
  final String rewardId;
  RedeemReward({required this.userId, required this.rewardId});
}

// Bloc
class RewardsBloc extends Bloc<RewardsEvent, RewardsState> {
  final RewardRepository _rewardRepository;

  RewardsBloc(this._rewardRepository) : super(RewardsInitial()) {
    on<LoadRewards>(_onLoadRewards);
    on<RedeemReward>(_onRedeemReward);
  }

  Future<void> _onLoadRewards(
      LoadRewards event, Emitter<RewardsState> emit) async {
    emit(RewardsLoading());
    try {
      final rewards = await _rewardRepository.getUserRewards(event.userId);
      final achievements =
          await _rewardRepository.getUserAchievements(event.userId);
      emit(RewardsLoaded(rewards: rewards, achievements: achievements));
    } catch (e) {
      emit(RewardsError(e.toString()));
    }
  }

  Future<void> _onRedeemReward(
      RedeemReward event, Emitter<RewardsState> emit) async {
    emit(RewardsLoading());
    try {
      await _rewardRepository.redeemReward(
        userId: event.userId,
        rewardId: event.rewardId,
      );
      // Reload rewards to reflect updated state
      final rewards = await _rewardRepository.getUserRewards(event.userId);
      final achievements =
          await _rewardRepository.getUserAchievements(event.userId);
      emit(RewardsLoaded(rewards: rewards, achievements: achievements));
    } catch (e) {
      emit(RewardsError(e.toString()));
    }
  }

  // Imperative methods for direct calls (used by RewardsScreen)
  Future<List<Reward>> getUserRewards(String userId) async {
    return await _rewardRepository.getUserRewards(userId);
  }

  Future<List<Reward>> getUserAchievements(String userId) async {
    return await _rewardRepository.getUserAchievements(userId);
  }

  Future<void> redeemReward({
    required String userId,
    required String rewardId,
  }) async {
    await _rewardRepository.redeemReward(
      userId: userId,
      rewardId: rewardId,
    );
  }
}
