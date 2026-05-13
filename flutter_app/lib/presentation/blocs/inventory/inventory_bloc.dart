import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../core/repositories/inventory_repository.dart';

// States
abstract class InventoryState {}

class InventoryInitial extends InventoryState {}

class InventoryLoading extends InventoryState {}

class InventoryLoaded extends InventoryState {
  final List<dynamic> items;
  InventoryLoaded(this.items);
}

class InventoryError extends InventoryState {
  final String message;
  InventoryError(this.message);
}

// Events
abstract class InventoryEvent {}

class LoadInventory extends InventoryEvent {
  final String? category;
  LoadInventory({this.category});
}

class AddInventoryItem extends InventoryEvent {
  final Map<String, dynamic> itemData;
  AddInventoryItem(this.itemData);
}

class UpdateInventoryItem extends InventoryEvent {
  final String itemId;
  final Map<String, dynamic> itemData;
  UpdateInventoryItem(this.itemId, this.itemData);
}

class DeleteInventoryItem extends InventoryEvent {
  final String itemId;
  DeleteInventoryItem(this.itemId);
}

// Bloc
class InventoryBloc extends Bloc<InventoryEvent, InventoryState> {
  final InventoryRepository _inventoryRepository;

  InventoryBloc(this._inventoryRepository) : super(InventoryInitial()) {
    on<LoadInventory>(_onLoadInventory);
    on<AddInventoryItem>(_onAddItem);
    on<UpdateInventoryItem>(_onUpdateItem);
    on<DeleteInventoryItem>(_onDeleteItem);
  }

  Future<void> _onLoadInventory(
      LoadInventory event, Emitter<InventoryState> emit) async {
    emit(InventoryLoading());
    try {
      final items = await _inventoryRepository.getItems(category: event.category);
      emit(InventoryLoaded(items));
    } catch (e) {
      emit(InventoryError(e.toString()));
    }
  }

  Future<void> _onAddItem(
      AddInventoryItem event, Emitter<InventoryState> emit) async {
    emit(InventoryLoading());
    try {
      await _inventoryRepository.addItem(event.itemData);
      final items = await _inventoryRepository.getItems();
      emit(InventoryLoaded(items));
    } catch (e) {
      emit(InventoryError(e.toString()));
    }
  }

  Future<void> _onUpdateItem(
      UpdateInventoryItem event, Emitter<InventoryState> emit) async {
    emit(InventoryLoading());
    try {
      await _inventoryRepository.updateItem(event.itemId, event.itemData);
      final items = await _inventoryRepository.getItems();
      emit(InventoryLoaded(items));
    } catch (e) {
      emit(InventoryError(e.toString()));
    }
  }

  Future<void> _onDeleteItem(
      DeleteInventoryItem event, Emitter<InventoryState> emit) async {
    emit(InventoryLoading());
    try {
      await _inventoryRepository.deleteItem(event.itemId);
      final items = await _inventoryRepository.getItems();
      emit(InventoryLoaded(items));
    } catch (e) {
      emit(InventoryError(e.toString()));
    }
  }
}
